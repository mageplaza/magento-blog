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
 * Post customer comments list
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Post_Customer_Comment_List extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * Post comments collection
     *
     * @var Mageplaza_BetterBlog_Model_Resource_Post_Comment_Post_Collection
     */
    protected $_collection;

    /**
     * Initializes collection
     *
     * @access public
     * @author Sam
     */
    protected function _construct()
    {
        $this->_collection = Mage::getResourceModel(
            'mageplaza_betterblog/post_comment_post_collection'
        );
        $this->_collection
            ->setStoreFilter(Mage::app()->getStore()->getId(), true)
            ->addAttributeToFilter('status', 1) //only active

            ->addStatusFilter(Mageplaza_BetterBlog_Model_Post_Comment::STATUS_APPROVED) //only approved comments
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId()) //only my comments
            ->setDateOrder();
    }

    /**
     * Gets collection items count
     *
     * @access public
     * @return int
     * @author Sam
     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * Get html code for toolbar
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @access protected
     * @return Mage_Core_Block_Abstract
     * @author Sam
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_post_comments.toolbar')
            ->setCollection($this->getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Get collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Resource_Post_Comment_Post_Collection
     * @author Sam
     */
    protected function _getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get collection
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Resource_Post_Comment_Post_Collection
     * @author Sam
     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    /**
     * Get review link
     *
     * @access public
     * @param mixed $comment
     * @return string
     * @author Sam
     */
    public function getCommentLink($comment)
    {
        if ($comment instanceof Varien_Object) {
            $comment = $comment->getCtCommentId();
        }
        return Mage::getUrl(
            'mageplaza_betterblog/post_customer_comment/view/',
            array('id' => $comment)
        );
    }

    /**
     * Get product link
     *
     * @access public
     * @param mixed $comment
     * @return string
     * @author Sam
     */
    public function getPostLink($comment)
    {
        return $comment->getPostUrl();
    }

    /**
     * Format date in short format
     *
     * @access public
     * @param $date
     * @return string
     * @author Sam
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }
}
