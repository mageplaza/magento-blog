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
 * Category post model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Category_Post extends Mage_Core_Model_Abstract
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
        $this->_init('mageplaza_betterblog/category_post');
    }

    /**
     * Save data for category - post relation
     * @access public
     * @param  Mageplaza_BetterBlog_Model_Category $category
     * @return Mageplaza_BetterBlog_Model_Category_Post
     * @author Sam
     */
    public function saveCategoryRelation($category)
    {
        $data = $category->getPostsData();
        if (!is_null($data)) {
            $this->_getResource()->saveCategoryRelation($category, $data);
        }
        return $this;
    }

    /**
     * get  for category
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Post_Collection
     * @author Sam
     */
    public function getPostsCollection($category)
    {
        $collection = Mage::getResourceModel('mageplaza_betterblog/category_post_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}
