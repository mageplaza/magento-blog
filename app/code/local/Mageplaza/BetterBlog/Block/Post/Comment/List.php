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
class Mageplaza_BetterBlog_Block_Post_Comment_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Sam
     */
    public function __construct()
    {
        parent::__construct();
        $post = $this->getPost();
        $comments = Mage::getResourceModel('mageplaza_betterblog/post_comment_collection')
            ->addFieldToFilter('post_id', $post->getId())
            ->addStoreFilter(Mage::app()->getStore())
             ->addFieldToFilter('status', 1);
        $comments->setOrder('created_at', 'asc');
        $this->setComments($comments);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Post_Comment_List
     * @author Sam
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'mageplaza_betterblog.post.html.pager'
        )
        ->setCollection($this->getComments());
        $this->setChild('pager', $pager);
        $this->getComments()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
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
}
