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
 * Post comment list block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author Sam
 */
class Mageplaza_BetterBlog_Block_Post_Comment_Facebook extends Mage_Core_Block_Template
{

    /**
     * get the current post
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Post
     * @author Sam
     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }

    public function getCurrentUrl()
    {
        return  $this->getPost()->getPostUrl();
    }


    public function getNumberOfComments()
    {
        return ($number = Mage::helper('mageplaza_betterblog/config')->getCommentConfig('facebook_number_comment')) ? $number : 10;

    }
    public function getAppId()
    {
        return Mage::helper('mageplaza_betterblog/config')->getCommentConfig('facebook_appid');
    }

    public function getColor()
    {
        return Mage::helper('mageplaza_betterblog/config')->getCommentConfig('colorscheme');
    }

    public function getOrderBy()
    {
        return Mage::helper('mageplaza_betterblog/config')->getCommentConfig('facebook_order_by');
    }






}
