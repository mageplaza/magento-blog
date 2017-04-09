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
 * Post - Category relation model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Post_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @return void
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Sam
     */
    protected function  _construct()
    {
        $this->_init('mageplaza_betterblog/post_category', 'rel_id');
    }

    /**
     * Save post - category relations
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Post $post
     * @param array $data
     * @return Mageplaza_BetterBlog_Model_Resource_Post_Category
     * @author Sam
     */
    public function savePostRelation($post, $categoryIds)
    {
        if (is_null($categoryIds)) {
            return $this;
        }
        $oldCategories = $post->getSelectedCategories();
        $oldCategoryIds = array();
        foreach ($oldCategories as $category) {
            $oldCategoryIds[] = $category->getId();
        }
        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);
        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }
                $data[] = array(
                    'category_id' => (int)$categoryId,
                    'post_id'  => (int)$post->getId(),
                    'position'=> 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->getMainTable(), $data);
            }
        }
        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = array(
                    'post_id = ?'  => (int)$post->getId(),
                    'category_id = ?' => (int)$categoryId,
                );
                $write->delete($this->getMainTable(), $where);
            }
        }
        return $this;
    }
}
