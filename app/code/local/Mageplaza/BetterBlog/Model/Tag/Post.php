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
 * Tag post model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Tag_Post extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Sam
     */
    protected function _construct()
    {
        $this->_init('mageplaza_betterblog/tag_post');
    }

    /**
     * Save data for tag - post relation
     * @access public
     * @param  Mageplaza_BetterBlog_Model_Tag $tag
     * @return Mageplaza_BetterBlog_Model_Tag_Post
     * @author Sam
     */
    public function saveTagRelation($tag)
    {
        $data = $tag->getPostsData();
        if (!is_null($data)) {
            $this->_getResource()->saveTagRelation($tag, $data);
        }
        return $this;
    }

    /**
     * get  for tag
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Tag $tag
     * @return Mageplaza_BetterBlog_Model_Resource_Tag_Post_Collection
     * @author Sam
     */
    public function getPostsCollection($tag)
    {
        $collection = Mage::getResourceModel('mageplaza_betterblog/tag_post_collection')
            ->addTagFilter($tag);
        return $collection;
    }
}
