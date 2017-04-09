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
 * Tag edit form tab
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tab_Form
     * @author Sam
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('tag_');
        $form->setFieldNameSuffix('tag');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'tag_form',
            array('legend' => Mage::helper('mageplaza_betterblog')->__('Tag'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('mageplaza_betterblog')->__('Name'),
                'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'description',
            'textarea',
            array(
                'label' => Mage::helper('mageplaza_betterblog')->__('Description'),
                'name'  => 'description',

           )
        );
        $fieldset->addField(
            'url_key',
            'text',
            array(
                'label' => Mage::helper('mageplaza_betterblog')->__('Url key'),
                'name'  => 'url_key',
                'note'  => Mage::helper('mageplaza_betterblog')->__('Relative to Website Base URL')
            )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('mageplaza_betterblog')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('mageplaza_betterblog')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('mageplaza_betterblog')->__('Disabled'),
                    ),
                ),
            )
        );
        $fieldset->addField(
            'in_rss',
            'select',
            array(
                'label'  => Mage::helper('mageplaza_betterblog')->__('Show in rss'),
                'name'   => 'in_rss',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('mageplaza_betterblog')->__('Yes'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('mageplaza_betterblog')->__('No'),
                    ),
                ),
            )
        );
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_tag')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_tag')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getTagData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getTagData());
            Mage::getSingleton('adminhtml/session')->setTagData(null);
        } elseif (Mage::registry('current_tag')) {
            $formValues = array_merge($formValues, Mage::registry('current_tag')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
