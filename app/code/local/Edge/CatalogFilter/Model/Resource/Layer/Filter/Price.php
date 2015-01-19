<?php

class Edge_CatalogFilter_Model_Resource_Layer_Filter_Price extends Mage_Catalog_Model_Resource_Layer_Filter_Price
{
    /**
     * Apply price range filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Price
     */
    public function applyPriceRange($filter)
    {
        if (!Mage::helper('catalogfilter')->priceIsSlider()) {
            return parent::applyPriceRange($filter);
        }

        $interval = $filter->getInterval();
        if (!$interval) {
            return $this;
        }

        list($from, $to) = $interval;
        if ($from === '' && $to === '') {
            return $this;
        }

        $select = $filter->getLayer()->getProductCollection()->getSelect();
        $priceExpr = $this->_getPriceExpression($filter, $select, false);

        if ($to !== '') {
            $to = (float)$to;
            if ($from == $to) {
                $to += self::MIN_POSSIBLE_PRICE;
            }
        }

        if ($from !== '') {
            $select->where($priceExpr . ' >= ' . $this->_getComparingValue($from, $filter));
        }
        if ($to !== '') {
            $select->where($priceExpr . ' <= ' . $this->_getComparingValue($to, $filter, false));
        }

        return $this;
    }

    /**
     * Load range of product prices
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param int $limit
     * @param null|int $offset
     * @param null|int $lowerPrice
     * @param null|int $upperPrice
     * @return array
     */
    public function loadPrices($filter, $limit, $offset = null, $lowerPrice = null, $upperPrice = null)
    {
        if (!Mage::helper('catalogfilter')->priceIsSlider()) {
            return parent::loadPrices($filter, $limit, $offset, $lowerPrice, $upperPrice);
        }

        $select = $this->_getSelect($filter);
        $priceExpression = $this->_getPriceExpression($filter, $select);
        $select->columns(array(
            'min_price_expr' => $this->_getFullPriceExpression($filter, $select)
        ));
        if (!is_null($lowerPrice)) {
            $select->where("$priceExpression >= " . $this->_getComparingValue($lowerPrice, $filter));
        }
        if (!is_null($upperPrice)) {
            $select->where("$priceExpression <= " . $this->_getComparingValue($upperPrice, $filter, false));
        }
        $select->order("$priceExpression ASC")->limit($limit, $offset);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Load range of product prices, preceding the price
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param float $price
     * @param int $index
     * @return array|false
     */
    public function loadPreviousPrices($filter, $price, $index, $lowerPrice = null)
    {
        if (!Mage::helper('catalogfilter')->priceIsSlider()) {
            return parent::loadPreviousPrices($filter, $price, $index, $lowerPrice);
        }

        $select = $this->_getSelect($filter);
        $priceExpression = $this->_getPriceExpression($filter, $select);
        $select->columns('COUNT(*)')->where("$priceExpression <= " . $this->_getComparingValue($price, $filter, false));
        if (!is_null($lowerPrice)) {
            $select->where("$priceExpression >= " . $this->_getComparingValue($lowerPrice, $filter));
        }
        $offset = $this->_getReadAdapter()->fetchOne($select);
        if (!$offset) {
            return false;
        }

        return $this->loadPrices($filter, $index - $offset + 1, $offset - 1, $lowerPrice);
    }

    /**
     * Load range of product prices, next to the price
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param float $price
     * @param int $rightIndex
     * @param null|int $upperPrice
     * @return array
     */
    public function loadNextPrices($filter, $price, $rightIndex, $upperPrice = null)
    {
        if (!Mage::helper('catalogfilter')->priceIsSlider()) {
            return parent::loadNextPrices($filter, $price, $rightIndex, $upperPrice);
        }

        $select = $this->_getSelect($filter);

        $pricesSelect = clone $select;
        $priceExpression = $this->_getPriceExpression($filter, $pricesSelect);

        $select->columns('COUNT(*)')->where("$priceExpression > " . $this->_getComparingValue($price, $filter, false));
        if (!is_null($upperPrice)) {
            $select->where("$priceExpression < " . $this->_getComparingValue($upperPrice, $filter, false));
        }
        $offset = $this->_getReadAdapter()->fetchOne($select);
        if (!$offset) {
            return false;
        }

        $pricesSelect
            ->columns(array(
                'min_price_expr' => $this->_getFullPriceExpression($filter, $pricesSelect)
            ))
            ->where("$priceExpression >= " . $this->_getComparingValue($price, $filter));
        if (!is_null($upperPrice)) {
            $pricesSelect->where("$priceExpression < " . $this->_getComparingValue($upperPrice, $filter));
        }
        $pricesSelect->order("$priceExpression DESC")->limit($rightIndex - $offset + 1, $offset - 1);

        return array_reverse($this->_getReadAdapter()->fetchCol($pricesSelect));
    }

    /**
     * Apply attribute filter to product collection
     *
     * @deprecated since 1.7.0.0
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param int $range
     * @param int $index    the range factor
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Price
     */
    public function applyFilterToCollection($filter, $range, $index)
    {
        if (!Mage::helper('catalogfilter')->priceIsSlider()) {
            return parent::applyFilterToCollection($filter, $range, $index);
        }

        $select = $filter->getLayer()->getProductCollection()->getSelect();
        $priceExpr = $this->_getPriceExpression($filter, $select);
        $filter->getLayer()->getProductCollection()
            ->getSelect()
            ->where($priceExpr . ' >= ' . $this->_getComparingValue(($range * ($index - 1)), $filter))
            ->where($priceExpr . ' <= ' . $this->_getComparingValue(($range * $index), $filter, false));

        return $this;
    }
}
