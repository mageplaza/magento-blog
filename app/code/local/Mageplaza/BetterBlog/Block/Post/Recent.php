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
 * Post list block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author Sam
 */
class Mageplaza_BetterBlog_Block_Post_Recent extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Sam
     */
    public function _construct()
    {
        parent::_construct();
        $posts = Mage::getResourceModel('mageplaza_betterblog/post_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1);
        $posts->setOrder('created_at', 'desc');
        $count = $this->getPostCount() ? $this->getPostCount() : 5;
        $posts->setPageSize($count);
        $this->setPosts($posts);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Post_List
     * @author Sam
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'mageplaza_betterblog.post.html.pager'
        )
            ->setCollection($this->getPosts());
        $this->setChild('pager', $pager);
        $this->getPosts()->load();
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
}
