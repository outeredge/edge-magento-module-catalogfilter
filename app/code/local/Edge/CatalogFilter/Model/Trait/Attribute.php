<?php

trait Edge_CatalogFilter_Model_Trait_Attribute
{
    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }

        if ($this->getAttributeModel()->getFilterType() === Edge_CatalogFilter_Helper_Data::FILTER_TYPE_MULTIPLE) {
            if ($filter){
                $filters = explode('-', $filter);
                $this->_getResource()->applyMultipleFilterToCollection($this, $filters);
            }
        }
        else {
            $text = $this->_getOptionText($filter);
            if ($filter && strlen($text)) {
                $this->_getResource()->applyFilterToCollection($this, $filter);
            }
        }
        return $this;
    }

    /**
     * Initialize filter items
     *
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
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $currentFilter = Mage::app()->getRequest()->getParam($this->_requestVar);

        $options = $attribute->getFrontend()->getSelectOptions();
        $optionsCount = $this->_getResource()->getCount($this);
        $data = array();
        foreach ($options as $option) {
            if (Mage::helper('core/string')->strlen($option['value'])) {
                if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                    if (!empty($optionsCount[$option['value']])) {
                        $data[] = array(
                            'label'  => $option['label'],
                            'value'  => $option['value'],
                            'count'  => $optionsCount[$option['value']],
                            'active' => $currentFilter === $option['value'] ? 1 : 0
                        );
                    }
                }
                else {
                    $data[] = array(
                        'label'  => $option['label'],
                        'value'  => $option['value'],
                        'count'  => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                        'active' => $currentFilter === $option['value'] ? 1 : 0
                    );
                }
            }
        }

        return $data;
    }
}