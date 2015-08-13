<?php

class Edge_CatalogFilter_Model_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category
{
    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        if (Mage::getStoreConfig('catalog/layered_navigation/category_filter_type') === Edge_CatalogFilter_Helper_Data::FILTER_TYPE_MULTIPLE) {
            $filters = explode('-', $filter);

            $collectionFilter = array();
            foreach ($filters as $categoryId) {
                $collectionFilter[] = array('finset' => $categoryId);
            }

            $this->getLayer()->getProductCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToFilter('category_id', $collectionFilter);

            Mage::getSingleton('catalogfilter/layer_state')->addFilter($this->getRequestVar(), $filters);
        }
        else {

            $category = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($filter);
            Mage::register('current_category_filter', $category, true);

            $this->getLayer()->getProductCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array('eq' => $filter));

            Mage::getSingleton('catalogfilter/layer_state')->addFilter($this->getRequestVar(), $filter);
        }

        return $this;
    }

    /**
     * Initialize filter items
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems()
    {
        $data = $this->_getItemsData();
        $items=array();
        foreach ($data as $itemData) {
            $item = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count']
            );
            $item->setActive($itemData['active']);
            $items[] = $item;
        }
        $this->_items = $items;
        return $this;
    }

    /**
     * Get data array for building category filter items
     * @return array
     */
    protected function _getItemsData()
    {
        $currentFilter = Mage::app()->getRequest()->getParam($this->getRequestVar());
        $categories = $this->getCategory()->getChildrenCategories();

        $this->getLayer()->getProductCollection()
            ->addCountToCategories($categories);

        $data = array();
        foreach ($categories as $category) {
            $data[] = array(
                'label'  => Mage::helper('core')->escapeHtml($category->getName()),
                'value'  => $category->getId(),
                'count'  => $category->getProductCount() ? $category->getProductCount() : 1,
                'active' => $currentFilter === $category->getId() ? 1 : 0
            );
        }

        return $data;
    }
}