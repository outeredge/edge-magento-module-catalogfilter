<?php

class Edge_CatalogFilter_Block_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price
{
    protected $_currentMinPrice;
    protected $_currentMaxPrice;

    /**
     * Initialize Price filter module
     *
     */
    public function __construct()
    {
        parent::__construct();

        if (Mage::getStoreConfig('catalog/layered_navigation/price_range_calculation') === 'slider') {
            $this->setTemplate('catalogfilter/price.phtml');
        }
    }

    public function getCurrentMinPrice()
    {
        if (is_null($this->_currentMinPrice)) {
            $this->_setCurrentPriceValues();
        }
        return $this->_currentMinPrice;
    }

    public function getCurrentMaxPrice()
    {
        if (is_null($this->_currentMaxPrice)) {
            $this->_setCurrentPriceValues();
        }
        return $this->_currentMaxPrice;
    }

    protected function _setCurrentPriceValues()
    {
        $price = $this->getRequest()->getParam($this->_filter->getRequestVar());
        if ($price) {
            $prices = explode('-', $price);
            $this->_currentMinPrice = $prices[0];
            $this->_currentMaxPrice = $prices[1];
        }
        else {
            $this->_currentMinPrice = $this->_filter->getMinPrice();
            $this->_currentMaxPrice = $this->_filter->getMaxPrice();
        }
    }
}