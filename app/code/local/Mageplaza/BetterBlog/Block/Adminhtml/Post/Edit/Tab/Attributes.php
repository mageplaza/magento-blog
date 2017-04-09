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
 * Post admin edit tab attributes block
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
*/
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the attributes for the form
     *
     * @access protected
     * @return void
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     * @author Sam
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_post'));
        $fieldset = $form->addFieldset(
            'info',
            array(
                'legend' => Mage::helper('mageplaza_betterblog')->__('Post Information'),
                'class' => 'fieldset-wide',
            )
        );
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute) {
            $attribute->setEntity(Mage::getResourceModel('mageplaza_betterblog/post'));
        }
        $this->_setFieldset($attributes, $fieldset, array());
        $formValues = Mage::registry('current_post')->getData();
        if (!Mage::registry('current_post')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($formValues[$attribute->getAttributeCode()])) {
                    $formValues[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }
        $form->addValues($formValues);
        $form->setFieldNameSuffix('post');
        $this->setForm($form);
    }

    /**
     * prepare layout
     *
     * @access protected
     * @return void
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareLayout()
     * @author Sam
     */
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('mageplaza_betterblog/adminhtml_betterblog_renderer_fieldset_element')
        );
    }

    /**
     * get the additional element types for form
     *
     * @access protected
     * @return array()
     * @see Mage_Adminhtml_Block_Widget_Form::_getAdditionalElementTypes()
     * @author Sam
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'     => Mage::getConfig()->getBlockClassName(
                'mageplaza_betterblog/adminhtml_post_helper_file'
            ),
            'image'    => Mage::getConfig()->getBlockClassName(
                'mageplaza_betterblog/adminhtml_post_helper_image'
            ),
            'textarea' => Mage::getConfig()->getBlockClassName(
                'adminhtml/catalog_helper_form_wysiwyg'
            )
        );
    }

    /**
     * get current entity
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Post
     * @author Sam
     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }
}
