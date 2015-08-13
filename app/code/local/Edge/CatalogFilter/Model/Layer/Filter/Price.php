<?php

class Edge_CatalogFilter_Model_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    protected $_minPrice;
    protected $_maxPrice;

    public function getMinPrice()
    {
        if (is_null($this->_minPrice)) {
            $this->_prepareRangeData();
        }
        return $this->_minPrice;
    }

    public function getMaxPrice()
    {
        if (is_null($this->_maxPrice)) {
            $this->_prepareRangeData();
        }
        return $this->_maxPrice;
    }

    protected function _prepareRangeData()
    {
        $select = clone $this->getLayer()->getProductCollection()->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::WHERE);

        $from = $select->getPart('from');
        foreach (array_keys($from) as $tableAlias){
            if ($tableAlias === 'e' || $tableAlias === 'cat_index' || $tableAlias === 'price_index') {
                continue;
            }
            unset($from[$tableAlias]);
        }
        $select->reset(Zend_Db_Select::FROM);
        $select->setPart(Zend_Db_Select::FROM, $from);

        $select->columns(array(
            'min' => 'ROUND(MIN(min_price))',
            'max' => 'ROUND(MAX(max_price))'
        ));

        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $row = $connection->fetchRow($select, $this->_bindParams, Zend_Db::FETCH_NUM);
        $this->_minPrice = (float)$row[0];
        $this->_maxPrice = (float)$row[1];

        return $this;
    }

    /*
     * Ensures the price attribute is always shown
     */
    public function getItemsCount()
    {
        if (Mage::helper('catalogfilter')->priceIsSlider()){
            return true;
        }
        return parent::getItemsCount();
    }

    /**
     * Apply price range filter
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param $filterBlock
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        if (!Mage::helper('catalogfilter')->priceIsSlider()) {
            return parent::apply($request, $filterBlock);
        }

        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        //validate filter
        $filterParams = explode(',', $filter);
        $filter = $this->_validateFilter($filterParams[0]);
        if (!$filter) {
            return $this;
        }

        list($from, $to) = $filter;

        $this->setInterval(array($from, $to));

        $priorFilters = array();
        for ($i = 1; $i < count($filterParams); ++$i) {
            $priorFilter = $this->_validateFilter($filterParams[$i]);
            if ($priorFilter) {
                $priorFilters[] = $priorFilter;
            } else {
                //not valid data
                $priorFilters = array();
                break;
            }
        }
        if ($priorFilters) {
            $this->setPriorIntervals($priorFilters);
        }

        $this->_applyPriceRange();

        Mage::getSingleton('catalogfilter/layer_state')->addFilter($this->getRequestVar(), $filter);

        return $this;
    }
}