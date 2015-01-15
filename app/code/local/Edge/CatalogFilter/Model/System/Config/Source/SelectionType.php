<?php

class Edge_CatalogFilter_Model_System_Config_Source_SelectionType
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'list',
                'label' => Mage::helper('adminhtml')->__('List')
            ),
            array(
                'value' => 'dropdown',
                'label' => Mage::helper('adminhtml')->__('Dropdown')
            )
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'list' => Mage::helper('adminhtml')->__('List'),
            'dropdown' => Mage::helper('adminhtml')->__('Dropdown'),
        );
    }
}