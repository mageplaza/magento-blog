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
 * Post view block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Post_View extends Mage_Core_Block_Template
{
    /**
     * get the current post
     *
     * @access public
     * @return mixed (Mageplaza_BetterBlog_Model_Post|null)
     * @author Sam
     */
    public function getCurrentPost()
    {
        return Mage::registry('current_post');
    }

    /**
     * get post in same topic
     * @return mixed
     */
    public function getPostTopics()
    {
        $post = $this->getCurrentPost();
        return $post->getPostsSameTopic();
    }

    /**
     * get topic label
     * @return mixed
     */
    public function getTopicLabel()
    {
        return $this->getCurrentPost()->getTopicLabel();
    }

    public function getPostsInCategory()
    {
        $currentPost = $this->getCurrentPost();
        $categories = $currentPost->getSelectedCategories();
        $category = null;
        if ($currentPost->getSelectedCategories()) {
            $category = isset($categories[0]) ? $categories[0] : null;
            $this->setCategoryName($category->getName());
            if ($category) {
                $config = Mage::helper('mageplaza_betterblog/config');
                $posts = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('entity_id',array('neq'=>$currentPost->getId()))
                ;
                $posts->setOrder('post_title', 'asc');
                $posts->addCategoryFilter($category->getId());
                $posts->setPageSize($config->getPostConfig('post_same_category_count'));
                $posts->unshiftOrder('related_category.position', 'ASC');
                return $posts;
            }


        }
        return null;
    }

    public function canShowPostSameCategory()
    {
        $config = Mage::helper('mageplaza_betterblog/config');
        return $config->getPostConfig('show_post_same_category');
    }

}
