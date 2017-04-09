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
 * Post comment edit form
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Comment_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare form
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Comment_Edit_Form
     * @author Sam
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id'         => 'edit_form',
                'action'     => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method'     => 'post',
                'enctype'    => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
