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
 * Post comments controller
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Post_Customer_CommentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     * Check customer authentication for some actions
     *
     * @access public
     * @author Sam
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * List comments
     *
     * @access public
     * @author Sam
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('mageplaza_betterblog/post_customer_comment/');
        }
        if ($block = $this->getLayout()->getBlock('post_customer_comment_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Post Comments'));

        $this->renderLayout();
    }

    /**
     * View comment
     *
     * @access public
     * @author Sam
     */
    public function viewAction()
    {
        $commentId = $this->getRequest()->getParam('id');
        $comment = Mage::getModel('mageplaza_betterblog/post_comment')->load($commentId);
        if (!$comment->getId() ||
            $comment->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId() ||
            $comment->getStatus() != Mageplaza_BetterBlog_Model_Post_Comment::STATUS_APPROVED) {
            $this->_forward('no-route');
            return;
        }
        $post = Mage::getModel('mageplaza_betterblog/post')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($comment->getPostId());
        if (!$post->getId() || $post->getStatus() != 1) {
            $this->_forward('no-route');
            return;
        }
        $stores = array(Mage::app()->getStore()->getId(), 0);
        if (count(array_intersect($stores, $comment->getStoreId())) == 0) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_comment', $comment);
        Mage::register('current_post', $post);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('mageplaza_betterblog/post_customer_comment/');
        }
        if ($block = $this->getLayout()->getBlock('customer_post_comment')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Post Comments'));
        $this->renderLayout();
    }
}
