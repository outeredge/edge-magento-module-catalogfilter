<?php

class Edge_CatalogFilter_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_priceIsSlider;

    public function priceIsSlider()
    {
        if (is_null($this->_priceIsSlider)) {
            $this->_priceIsSlider = Mage::getStoreConfig('catalog/layered_navigation/price_range_calculation') === 'slider';
        }
        return $this->_priceIsSlider;
    }
}