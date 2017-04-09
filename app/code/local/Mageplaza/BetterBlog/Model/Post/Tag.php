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
 * Post tag model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Post_Tag extends Mage_Core_Model_Abstract
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
        $this->_init('mageplaza_betterblog/post_tag');
    }

    /**
     * Save data for post - tag relation
     * @access public
     * @param  Mageplaza_BetterBlog_Model_Post $post
     * @return Mageplaza_BetterBlog_Model_Post_Tag
     * @author Sam
     */
    public function savePostRelation($post)
    {
        $data = $post->getTagsData();
        if (!is_null($data)) {
            $this->_getResource()->savePostRelation($post, $data);
//            foreach($data as $tagId => $info){
//                Mage::getModel('mageplaza_betterblog/tag')->updateTagCount($tagId);
//            }
        }
        return $this;
    }

    /**
     * get  for post
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Post $post
     * @return Mageplaza_BetterBlog_Model_Resource_Post_Tag_Collection
     * @author Sam
     */
    public function getTagsCollection($post)
    {
        $collection = Mage::getResourceModel('mageplaza_betterblog/post_tag_collection')
            ->addPostFilter($post);
        return $collection;
    }
}
