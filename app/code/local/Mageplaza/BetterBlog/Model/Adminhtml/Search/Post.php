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
 * Admin search model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Adminhtml_Search_Post extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Adminhtml_Search_Post
     * @author Sam
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('mageplaza_betterblog/post_collection')
            ->addAttributeToFilter('post_title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $post) {
            $arr[] = array(
                'id'          => 'post/1/'.$post->getId(),
                'type'        => Mage::helper('mageplaza_betterblog')->__('Post'),
                'name'        => $post->getPostTitle(),
                'description' => $post->getPostTitle(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/betterblog_post/edit',
                    array('id'=>$post->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
