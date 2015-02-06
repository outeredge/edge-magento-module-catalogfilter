<?php

class Edge_CatalogFilter_Model_Observer_Form
{
    public function addFilterTypeField(Varien_Event_Observer $observer)
    {
        $form = $observer->getEvent()->getForm();

        $fieldset = $form->getElement('front_fieldset');
        $isFilterable = $form->getElement('is_filterable');

        $filterType = $fieldset->addField('filter_type', 'select', array(
            'name'   => 'filter_type',
            'label'  => Mage::helper('adminhtml')->__('Filter Type'),
            'values' => array(
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
            )
        ), $isFilterable->getName());

        $allBlocks = Mage::app()->getLayout()->getAllBlocks();
        foreach ($allBlocks as $theBlock){
            if ($theBlock instanceof Mage_Adminhtml_Block_Widget_Form_Element_Dependence){
                $formAfter = $theBlock;
            }
        }

        if ($formAfter){
            $formAfter
                 ->addFieldMap($isFilterable->getHtmlId(), $isFilterable->getName())
                 ->addFieldMap($filterType->getHtmlId(), $filterType->getName())
                 ->addFieldDependence(
                     $filterType->getName(),
                     $isFilterable->getName(),
                     array('1','2'));
        }
    }
}
