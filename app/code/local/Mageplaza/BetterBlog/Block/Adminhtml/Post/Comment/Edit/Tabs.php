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
 * Post comment admin edit tabs
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Sam
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('post_comment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mageplaza_betterblog')->__('Post Comment'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tabs
     * @author Sam
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_post_comment',
            array(
                'label'   => Mage::helper('mageplaza_betterblog')->__('Post comment'),
                'title'   => Mage::helper('mageplaza_betterblog')->__('Post comment'),
                'content' => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_post_comment_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_post_comment',
                array(
                    'label'   => Mage::helper('mageplaza_betterblog')->__('Store views'),
                    'title'   => Mage::helper('mageplaza_betterblog')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'mageplaza_betterblog/adminhtml_post_comment_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve comment
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Post_Comment
     * @author Sam
     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }
}
