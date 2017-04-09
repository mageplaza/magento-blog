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
 * Category - Post relation resource model collection
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Category_Post_Collection extends Mageplaza_BetterBlog_Model_Resource_Post_Collection
{
    /**
     * remember if fields have been joined
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Post_Collection
     * @author Sam
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('mageplaza_betterblog/category_post')),
                'related.post_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add category filter
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category | int $category
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Post_Collection
     * @author Sam
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Mageplaza_BetterBlog_Model_Category) {
            $category = $category->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.category_id = ?', $category);
        return $this;
    }
}
