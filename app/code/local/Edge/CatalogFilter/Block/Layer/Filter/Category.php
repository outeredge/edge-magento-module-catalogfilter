<?php

class Edge_CatalogFilter_Block_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalogfilter/filter.phtml');
    }

    protected function _getFilterType()
    {
        return Mage::getStoreConfig('catalog/layered_navigation/category_filter_type');
    }

    public function isDropdown()
    {
        return $this->_getFilterType() === Edge_CatalogFilter_Model_Layer_Filter_Attribute::FILTER_TYPE_SINGLE;
    }

    public function isMultiple()
    {
        return $this->_getFilterType() === Edge_CatalogFilter_Model_Layer_Filter_Attribute::FILTER_TYPE_MULTIPLE;
    }

    public function isUnset()
    {
        return !$this->getRequest()->getParam($this->_filter->getRequestVar());
    }
}