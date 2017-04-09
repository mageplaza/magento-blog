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
 * Post collection resource model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Post_Collection extends Mage_Catalog_Model_Resource_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Sam
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('mageplaza_betterblog/post');
    }

    /**
     * get posts as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     * @author Sam
     */
    protected function _toOptionArray($valueField='entity_id', $labelField='post_title', $additional=array())
    {
        $this->addAttributeToSelect('post_title');
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * get options hash
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @return array
     * @author Sam
     */
    protected function _toOptionHash($valueField='entity_id', $labelField='post_title')
    {
        $this->addAttributeToSelect('post_title');
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * add the category filter to collection
     *
     * @access public
     * @param mixed (Mageplaza_BetterBlog_Model_Category|int) $category
     * @return Mageplaza_BetterBlog_Model_Resource_Post_Collection
     * @author Sam
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Mageplaza_BetterBlog_Model_Category) {
            $category = $category->getId();
        }
        if (!isset($this->_joinedFields['category'])) {
            $this->getSelect()->join(
                array('related_category' => $this->getTable('mageplaza_betterblog/post_category')),
                'related_category.post_id = e.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_category.category_id = ?', $category);
            $this->_joinedFields['category'] = true;
        }
        return $this;
    }

    /**
     * add the tag filter to collection
     *
     * @access public
     * @param mixed (Mageplaza_BetterBlog_Model_Tag|int) $tag
     * @return Mageplaza_BetterBlog_Model_Resource_Post_Collection
     * @author Sam
     */
    public function addTagFilter($tag)
    {
        if ($tag instanceof Mageplaza_BetterBlog_Model_Tag) {
            $tag = $tag->getId();
        }
        if (!isset($this->_joinedFields['tag'])) {
            $this->getSelect()->join(
                array('related_tag' => $this->getTable('mageplaza_betterblog/post_tag')),
                'related_tag.post_id = e.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_tag.tag_id = ?', $tag);
            $this->_joinedFields['tag'] = true;
        }
        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select
     * @author Sam
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
