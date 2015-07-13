<?php

trait Edge_CatalogFilter_Block_Trait_Attribute
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalogfilter/filter.phtml');
    }

    public function isDropdown()
    {
        return $this->_filter->getAttributeModel()->getFilterType() === Edge_CatalogFilter_Helper_Data::FILTER_TYPE_SINGLE;
    }

    public function isMultiple()
    {
        return $this->_filter->getAttributeModel()->getFilterType() === Edge_CatalogFilter_Helper_Data::FILTER_TYPE_MULTIPLE;
    }

    public function isUnset()
    {
        return !$this->getRequest()->getParam($this->_filter->getRequestVar());
    }
}

