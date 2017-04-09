<?php
/**
 * Mageplaza_BetterBlog extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Mageplaza
 * @package        Mageplaza_BetterBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Adminhtml post attribute edit page main tab
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Attribute_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding product form elements for editing attribute
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Attribute_Edit_Tab_Main
     * @author Sam
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            array(
                'value' => 'image',
                'label' => Mage::helper('mageplaza_betterblog')->__('Image')
            ),
            array(
                'value' => 'file',
                'label' => Mage::helper('mageplaza_betterblog')->__('File')
            )
        );
        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_post_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE   =>
                Mage::helper('mageplaza_betterblog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>
                Mage::helper('mageplaza_betterblog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL  =>
                Mage::helper('mageplaza_betterblog')->__('Global'),
        );

        $fieldset->addField(
            'is_global',
            'select',
            array(
                'name'  => 'is_global',
                'label' => Mage::helper('mageplaza_betterblog')->__('Scope'),
                'title' => Mage::helper('mageplaza_betterblog')->__('Scope'),
                'note'  => Mage::helper('mageplaza_betterblog')->__('Declare attribute value saving scope'),
                'values'=> $scopes
            ),
            'attribute_code'
        );
        $fieldset->addField(
            'position',
            'text',
            array(
                'name'  => 'position',
                'label' => Mage::helper('mageplaza_betterblog')->__('Position'),
                'title' => Mage::helper('mageplaza_betterblog')->__('Position'),
                'note'  => Mage::helper('mageplaza_betterblog')->__('Position in the admin form'),
            ),
            'is_global'
        );
        $fieldset->addField(
            'note',
            'textarea',
            array(
                'name'  => 'note',
                'label' => Mage::helper('mageplaza_betterblog')->__('Note'),
                'title' => Mage::helper('mageplaza_betterblog')->__('Note'),
                'note'  => Mage::helper('mageplaza_betterblog')->__('Text to appear below the input.'),
            ),
            'position'
        );

        $fieldset->removeField('is_unique');
        // frontend properties fieldset
        $fieldset = $form->addFieldset(
            'front_fieldset',
            array(
                'legend'=>Mage::helper('mageplaza_betterblog')->__('Frontend Properties')
            )
        );
        $fieldset->addField(
            'is_wysiwyg_enabled',
            'select',
            array(
                'name' => 'is_wysiwyg_enabled',
                'label' => Mage::helper('mageplaza_betterblog')->__('Enable WYSIWYG'),
                'title' => Mage::helper('mageplaza_betterblog')->__('Enable WYSIWYG'),
                'values' => $yesnoSource,
            )
        );
        Mage::dispatchEvent(
            'mageplaza_betterblog_adminhtml_post_attribute_edit_prepare_form',
            array(
                'form'      => $form,
                'attribute' => $attributeObject
            )
        );
        return $this;
    }
}
