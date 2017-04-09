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
class Mageplaza_BetterBlog_Block_Post_Comment_Disqus extends Mage_Core_Block_Template
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

    public function getDisqusName()
    {
        return Mage::helper('mageplaza_betterblog/config')->getCommentConfig('disqus');
    }
}
