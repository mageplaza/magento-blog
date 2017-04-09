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
 * Tag collection resource model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Tag_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
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
        $this->_init('mageplaza_betterblog/tag');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Add filter by store
     *
     * @access public
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Mageplaza_BetterBlog_Model_Resource_Tag_Collection
     * @author Sam
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!isset($this->_joinedFields['store'])) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }
            if (!is_array($store)) {
                $store = array($store);
            }
            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }
            $this->addFilter('store', array('in' => $store), 'public');
            $this->_joinedFields['store'] = true;
        }
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Resource_Tag_Collection
     * @author Sam
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('mageplaza_betterblog/tag_store')),
                'main_table.entity_id = store_table.tag_id',
                array()
            )
            ->group('main_table.entity_id');
            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }

    /**
     * get tags as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     * @author Sam
     */
    protected function _toOptionArray($valueField='entity_id', $labelField='name', $additional=array())
    {
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
    protected function _toOptionHash($valueField='entity_id', $labelField='name')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * add the post filter to collection
     *
     * @access public
     * @param mixed (Mageplaza_BetterBlog_Model_Post|int) $post
     * @return Mageplaza_BetterBlog_Model_Resource_Tag_Collection
     * @author Sam
     */
    public function addPostFilter($post)
    {
        if ($post instanceof Mageplaza_BetterBlog_Model_Post) {
            $post = $post->getId();
        }
        if (!isset($this->_joinedFields['post'])) {
            $this->getSelect()->join(
                array('related_post' => $this->getTable('mageplaza_betterblog/tag_post')),
                'related_post.tag_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_post.post_id = ?', $post);
            $this->_joinedFields['post'] = true;
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
