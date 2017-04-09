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
 * Post attribute edit block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @author Sam
     */
    public function __construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'adminhtml_post_attribute';
        $this->_blockGroup = 'mageplaza_betterblog';

        parent::__construct();
        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'     => Mage::helper('mageplaza_betterblog')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save'
            ),
            100
        );
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('mageplaza_betterblog')->__('Save Post Attribute')
        );
        $this->_updateButton('save', 'onclick', 'saveAttribute()');

        if (!Mage::registry('entity_attribute')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton(
                'delete',
                'label',
                Mage::helper('mageplaza_betterblog')->__('Delete Post Attribute')
            );
        }
    }

    /**
     * get the header text for the form
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getHeaderText()
    {
        if (Mage::registry('entity_attribute')->getId()) {
            $frontendLabel = Mage::registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return Mage::helper('mageplaza_betterblog')->__('Edit Post Attribute "%s"', $this->escapeHtml($frontendLabel));
        } else {
            return Mage::helper('mageplaza_betterblog')->__('New Post Attribute');
        }
    }

    /**
     * get validation url for form
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    /**
     * get save url for form
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/'.$this->_controller.'/save', array('_current'=>true, 'back'=>null));
    }
}
