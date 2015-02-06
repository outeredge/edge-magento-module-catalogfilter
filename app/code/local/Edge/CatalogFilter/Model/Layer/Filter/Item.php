<?php

class Edge_CatalogFilter_Model_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->getFilter()->getAttributeModel()->getFilterType() === Edge_CatalogFilter_Model_Layer_Filter_Attribute::FILTER_TYPE_MULTIPLE) {

            // Return a remove url if we are filtering already
            if ($this->getActive()){
                return $this->getMultipleRemoveUrl();
            }

            $current = $this->_getCurrentValues();
            $value = $this->getValue();

            if (!empty($current)){
                if (!in_array($value, $current)) {
                    // Add this value to currentValues
                    $current[] = $value;
                }
                $value = implode('-', $current);
            }

            $query = array(
                $this->getFilter()->getRequestVar() => $value,
                Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null
            );
        }
        else{
            $query = array(
                $this->getFilter()->getRequestVar()=>$this->getValue(),
                Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
            );
        }
        return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getMultipleRemoveUrl()
    {
        $current = $this->_getCurrentValues();
        if (($key = array_search($this->getValue(), $current)) !== false) {
            unset($current[$key]);
        }
        if(empty($current)){
            $current = $this->getFilter()->getResetValue();
        } else {
            $current = implode('-', $current);
        }

        $query = array($this->getFilter()->getRequestVar() => $current);
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = true;
        return Mage::getUrl('*/*/*', $params);
    }

    public function getActive()
    {
        if ($this->getFilter()->getAttributeModel()->getFilterType() === Edge_CatalogFilter_Model_Layer_Filter_Attribute::FILTER_TYPE_MULTIPLE) {
            return in_array($this->getValue(), $this->_getCurrentValues());
        }
        return parent::getActive();
    }

    protected function _getCurrentValues()
    {
        $current = Mage::app()->getRequest()->getParam($this->getFilter()->getRequestVar());
        if (!$current){
            return array();
        }
        return explode('-', $current);
    }
}
