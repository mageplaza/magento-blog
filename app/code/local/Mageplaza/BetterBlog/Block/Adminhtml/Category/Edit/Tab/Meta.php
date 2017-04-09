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
 * meta information tab
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Category_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tab_Meta
     * @author Sam
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('category');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'tag_meta_form',
            array('legend' => Mage::helper('mageplaza_betterblog')->__('Meta information'))
        );
        $fieldset->addField(
            'meta_title',
            'text',
            array(
                'label' => Mage::helper('mageplaza_betterblog')->__('Meta-title'),
                'name'  => 'meta_title',
            )
        );
        $fieldset->addField(
            'meta_description',
            'textarea',
            array(
                'name'      => 'meta_description',
                'label'     => Mage::helper('mageplaza_betterblog')->__('Meta-description'),
              )
        );
        $fieldset->addField(
            'meta_keywords',
            'textarea',
            array(
                'name'      => 'meta_keywords',
                'label'     => Mage::helper('mageplaza_betterblog')->__('Meta-keywords'),
            )
        );
        $form->addValues(Mage::registry('current_category')->getData());
        return parent::_prepareForm();
    }
}
