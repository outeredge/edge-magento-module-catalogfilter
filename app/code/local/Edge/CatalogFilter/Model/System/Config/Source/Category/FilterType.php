<?php

class Edge_CatalogFilter_Model_System_Config_Source_Category_FilterType
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => null,
                'label' => Mage::helper('adminhtml')->__('Single List')
            ),
            array(
                'value' => Edge_CatalogFilter_Model_Layer_Filter_Attribute::FILTER_TYPE_SINGLE,
                'label' => Mage::helper('adminhtml')->__('Single Dropdown')
            ),
            array(
                'value' => Edge_CatalogFilter_Model_Layer_Filter_Attribute::FILTER_TYPE_MULTIPLE,
                'label' => Mage::helper('adminhtml')->__('Multiple List')
            )
        );
    }
}
