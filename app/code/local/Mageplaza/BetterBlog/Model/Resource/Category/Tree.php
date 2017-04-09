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
 * Category tree resource model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Category_Tree extends Varien_Data_Tree_Dbp
{
    const ID_FIELD        = 'entity_id';
    const PATH_FIELD      = 'path';
    const ORDER_FIELD     = 'order';
    const LEVEL_FIELD     = 'level';

    /**
     * Categories resource collection
     *
     * @var Mageplaza_BetterBlog_Model_Resource_Category_Collection
     */
    protected $_collection;
    protected $_storeId;

    /**
     * Inactive categories ids
     * @var array
     */

    protected $_inactiveCategoryIds  = null;

    /**
     * Initialize tree
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct(
            $resource->getConnection('mageplaza_betterblog_write'),
            $resource->getTableName('mageplaza_betterblog/category'),
            array(
                Varien_Data_Tree_Dbp::ID_FIELD    => 'entity_id',
                Varien_Data_Tree_Dbp::PATH_FIELD  => 'path',
                Varien_Data_Tree_Dbp::ORDER_FIELD => 'position',
                Varien_Data_Tree_Dbp::LEVEL_FIELD => 'level',
            )
        );
    }

    /**
     * Get categories collection
     *
     * @access public
     * @param boolean $sorted
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Collection
     * @author Sam
     */
    public function getCollection($sorted = false)
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_getDefaultCollection($sorted);
        }
        return $this->_collection;
    }
    /**
     * set the collection
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Resource_Category_Collection $collection
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Tree
     */
    public function setCollection($collection)
    {
        if (!is_null($this->_collection)) {
            destruct($this->_collection);
        }
        $this->_collection = $collection;
        return $this;
    }
    /**
     * get the default collection
     *
     * @access protected
     * @param boolean $sorted
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Collection
     */
    protected function _getDefaultCollection($sorted = false)
    {
        $collection = Mage::getModel('mageplaza_betterblog/category')->getCollection();
        if ($sorted) {
            if (is_string($sorted)) {
                $collection->setOrder($sorted);
            } else {
                $collection->setOrder('name');
            }
        }
        return $collection;
    }

    /**
     * Executing parents move method and cleaning cache after it
     *
     * @access public
     * @param unknown_type $category
     * @param unknown_type $newParent
     * @param unknown_type $prevNode
     * @author Sam
     */
    public function move($category, $newParent, $prevNode = null)
    {
        Mage::getResourceSingleton('mageplaza_betterblog/category')
            ->move($category->getId(), $newParent->getId());
        parent::move($category, $newParent, $prevNode);
        $this->_afterMove($category, $newParent, $prevNode);
    }

    /**
     * Move tree after
     *
     * @access protected
     * @param unknown_type $category
     * @param Varien_Data_Tree_Node $newParent
     * @param Varien_Data_Tree_Node $prevNode
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Tree
     */
    protected function _afterMove($category, $newParent, $prevNode)
    {
        Mage::app()->cleanCache(array(Mageplaza_BetterBlog_Model_Category::CACHE_TAG));
        return $this;
    }

    /**
     * Load whole category tree, that will include specified categories ids.
     *
     * @access public
     * @param array $ids
     * @param bool $addCollectionData
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Tree
     * @author Sam
     */
    public function loadByIds($ids, $addCollectionData = true)
    {
        $levelField = $this->_conn->quoteIdentifier('level');
        $pathField  = $this->_conn->quoteIdentifier('path');
        // load first two levels, if no ids specified
        if (empty($ids)) {
            $select = $this->_conn->select()
                ->from($this->_table, 'entity_id')
                ->where($levelField . ' <= 2');
            $ids = $this->_conn->fetchCol($select);
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        foreach ($ids as $key => $id) {
            $ids[$key] = (int)$id;
        }
        // collect paths of specified IDs and prepare to collect all their parents and neighbours
        $select = $this->_conn->select()
            ->from($this->_table, array('path', 'level'))
            ->where('entity_id IN (?)', $ids);
        $where = array($levelField . '=0' => true);

        foreach ($this->_conn->fetchAll($select) as $item) {
            $pathIds  = explode('/', $item['path']);
            $level = (int)$item['level'];
            while ($level > 0) {
                $pathIds[count($pathIds) - 1] = '%';
                $path = implode('/', $pathIds);
                $where["$levelField=$level AND $pathField LIKE '$path'"] = true;
                array_pop($pathIds);
                $level--;
            }
        }
        $where = array_keys($where);

        // get all required records
        if ($addCollectionData) {
            $select = $this->_createCollectionDataSelect();
        } else {
            $select = clone $this->_select;
            $select->order($this->_orderField . ' ' . Varien_Db_Select::SQL_ASC);
        }
        $select->where(implode(' OR ', $where));

        // get array of records and add them as nodes to the tree
        $arrNodes = $this->_conn->fetchAll($select);
        if (!$arrNodes) {
            return false;
        }
        $childrenItems = array();
        foreach ($arrNodes as $key => $nodeInfo) {
            $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
            array_pop($pathToParent);
            $pathToParent = implode('/', $pathToParent);
            $childrenItems[$pathToParent][] = $nodeInfo;
        }
        $this->addChildNodes($childrenItems, '', null);
        return $this;
    }

    /**
     * Load array of category parents
     *
     * @access public
     * @param string $path
     * @param bool $addCollectionData
     * @param bool $withRootNode
     * @return array
     * @author Sam
     */
    public function loadBreadcrumbsArray($path, $addCollectionData = true, $withRootNode = false)
    {
        $pathIds = explode('/', $path);
        if (!$withRootNode) {
            array_shift($pathIds);
        }
        $result = array();
        if (!empty($pathIds)) {
            if ($addCollectionData) {
                $select = $this->_createCollectionDataSelect(false);
            } else {
                $select = clone $this->_select;
            }
            $select
                ->where('main_table.entity_id IN(?)', $pathIds)
                ->order($this->_conn->getLengthSql('main_table.path') . ' ' . Varien_Db_Select::SQL_ASC);
            $result = $this->_conn->fetchAll($select);
        }
        return $result;
    }

    /**
     * Obtain select for categories
     * By default everything from entity table is selected
     * + name
     *
     * @access public
     * @param bool $sorted
     * @param array $optionalAttributes
     * @return Zend_Db_Select
     * @author Sam
     */
    protected function _createCollectionDataSelect($sorted = true)
    {
        $select = $this->_getDefaultCollection($sorted ? $this->_orderField : false)->getSelect();
        $categoriesTable = Mage::getSingleton('core/resource')
            ->getTableName('mageplaza_betterblog/category');
        $subConcat = $this->_conn->getConcatSql(array('main_table.path', $this->_conn->quote('/%')));
        $subSelect = $this->_conn->select()
            ->from(array('see' => $categoriesTable), null)
            ->where('see.entity_id = main_table.entity_id')
            ->orWhere('see.path LIKE ?', $subConcat);
        return $select;
    }

    /**
     * Get real existing category ids by specified ids
     *
     * @access public
     * @param array $ids
     * @return array
     * @author Sam
     */
    public function getExistingCategoryIdsBySpecifiedIds($ids)
    {
        if (empty($ids)) {
            return array();
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $select = $this->_conn->select()
            ->from($this->_table, array('entity_id'))
            ->where('entity_id IN (?)', $ids);
        return $this->_conn->fetchCol($select);
    }

    /**
     * add collection data
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Resource_Category_Collection $collection
     * @param boolean $sorted
     * @param array $exclude
     * @param boolean $toLoad
     * @param boolean $onlyActive
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Tree
     * @author Sam
     */
    public function addCollectionData(
        $collection = null,
        $sorted = false,
        $exclude = array(),
        $toLoad = true,
        $onlyActive = false
    )
    {
        if (is_null($collection)) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }
        if (!is_array($exclude)) {
            $exclude = array($exclude);
        }
        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        $collection->addIdFilter($nodeIds);
        if ($onlyActive) {
            $disabledIds = $this->_getDisabledIds($collection);
            if ($disabledIds) {
                $collection->addFieldToFilter('entity_id', array('nin' => $disabledIds));
            }
            $collection->addFieldToFilter('status', 1);
        }
        if ($toLoad) {
            $collection->load();
            foreach ($collection as $category) {
                if ($this->getNodeById($category->getId())) {
                    $this->getNodeById($category->getId())->addData($category->getData());
                }
            }
            foreach ($this->getNodes() as $node) {
                if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                    $this->removeNode($node);
                }
            }
        }
        return $this;
    }

    /**
     * Add inactive categories ids
     *
     * @access public
     * @param unknown_type $ids
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Tree
     * @author Sam
     */
    public function addInactiveCategoryIds($ids)
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }
        $this->_inactiveCategoryIds = array_merge($ids, $this->_inactiveCategoryIds);
        return $this;
    }

    /**
     * Retrieve inactive categories ids
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Tree
     * @author Sam
     */
    protected function _initInactiveCategoryIds()
    {
        $this->_inactiveCategoryIds = array();
        return $this;
    }
    /**
     * Retrieve inactive categories ids
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getInactiveCategoryIds()
    {
        if (!is_array($this->_inactiveCategoryIds)) {
            $this->_initInactiveCategoryIds();
        }
        return $this->_inactiveCategoryIds;
    }

    /**
     * Return disable category ids
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Resource_Category_Collection $collection
     * @return array
     * @author Sam
     */
    protected function _getDisabledIds($collection)
    {
        $this->_inactiveItems = $this->getInactiveCategoryIds();
        $this->_inactiveItems = array_merge(
            $this->_getInactiveItemIds($collection),
            $this->_inactiveItems
        );
        $allIds = $collection->getAllIds();
        $disabledIds = array();

        foreach ($allIds as $id) {
            $parents = $this->getNodeById($id)->getPath();
            foreach ($parents as $parent) {
                if (!$this->_getItemIsActive($parent->getId())) {
                    $disabledIds[] = $id;
                    continue;
                }
            }
        }
        return $disabledIds;
    }

    /**
     * Retrieve inactive category item ids
     *
     * @access protecte
     * @param Mageplaza_BetterBlog_Model_Resource_Category_Collection $collection
     * @return array
     * @author Sam
     */
    protected function _getInactiveItemIds($collection)
    {
        $filter = $collection->getAllIdsSql();
        $table = Mage::getSingleton('core/resource')->getTable('mageplaza_betterblog/category');
        $bind = array(
            'cond' => 0,
        );
        $select = $this->_conn->select()
            ->from(array('d'=>$table), array('d.entity_id'))
            ->where('d.entity_id IN (?)', new Zend_Db_Expr($filter))
            ->where('status = :cond');
        return $this->_conn->fetchCol($select, $bind);
    }

    /**
     * Check is category items active
     *
     * @access protecte
     * @param int $id
     * @return boolean
     * @author Sam
     */
    protected function _getItemIsActive($id)
    {
        if (!in_array($id, $this->_inactiveItems)) {
            return true;
        }
        return false;
    }
}