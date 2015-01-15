<?php

class Edge_CatalogFilter_Block_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalogfilter/filter.phtml');
    }

    public function isDropdown()
    {
        return Mage::getStoreConfig('catalog/layered_navigation/selection_type') === 'dropdown';
    }

    public function isUnset()
    {
        return !$this->getRequest()->getParam($this->_filter->getRequestVar());
    }
}