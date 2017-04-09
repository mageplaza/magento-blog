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
 * Category resource model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Category tree object
     * @var Varien_Data_Tree_Db
     */
    protected $_tree;

    /**
     * constructor
     *
     * @access public
     * @author Sam
     */
    public function _construct()
    {
        $this->_init('mageplaza_betterblog/category', 'entity_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @access public
     * @param int $categoryId
     * @return array
     * @author Sam
     */
    public function lookupStoreIds($categoryId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('mageplaza_betterblog/category_store'), 'store_id')
            ->where('category_id = ?', (int)$categoryId);
        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return Mageplaza_BetterBlog_Model_Resource_Category
     * @author Sam
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mageplaza_BetterBlog_Model_Category $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('betterblog_category_store' => $this->getTable('mageplaza_betterblog/category_store')),
                $this->getMainTable() . '.entity_id = betterblog_category_store.category_id',
                array()
            )
            ->where('betterblog_category_store.store_id IN (?)', $storeIds)
            ->order('betterblog_category_store.store_id DESC')
            ->limit(1);
        }
        return $select;
    }

    /**
     * Retrieve category tree object
     *
     * @access protected
     * @return Varien_Data_Tree_Db
     * @author Sam
     */
    protected function _getTree()
    {
        if (!$this->_tree) {
            $this->_tree = Mage::getResourceModel('mageplaza_betterblog/category_tree')->load();
        }
        return $this->_tree;
    }

    /**
     * Process category data before delete
     * update children count for parent category
     * delete child categories
     *
     * @access protected
     * @param Varien_Object $object
     * @return Mageplaza_BetterBlog_Model_Resource_Category
     * @author Sam
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeDelete($object);
        /**
         * Update children count for all parent categories
         */
        $parentIds = $object->getParentIds();
        if ($parentIds) {
            $childDecrease = $object->getChildrenCount() + 1; // +1 is itself
            $data = array('children_count' => new Zend_Db_Expr('children_count - ' . $childDecrease));
            $where = array('entity_id IN(?)' => $parentIds);
            $this->_getWriteAdapter()->update($this->getMainTable(), $data, $where);
        }
        $this->deleteChildren($object);
        return $this;
    }

    /**
     * Delete children categories of specific category
     *
     * @access public
     * @param Varien_Object $object
     * @return Mageplaza_BetterBlog_Model_Resource_Category
     * @author Sam
     */
    public function deleteChildren(Varien_Object $object)
    {
        $adapter = $this->_getWriteAdapter();
        $pathField = $adapter->quoteIdentifier('path');
        $select = $adapter->select()
            ->from($this->getMainTable(), array('entity_id'))
            ->where($pathField . ' LIKE :c_path');
        $childrenIds = $adapter->fetchCol($select, array('c_path' => $object->getPath() . '/%'));
        if (!empty($childrenIds)) {
            $adapter->delete(
                $this->getMainTable(),
                array('entity_id IN (?)' => $childrenIds)
            );
        }
        /**
         * Add deleted children ids to object
         * This data can be used in after delete event
         */
        $object->setDeletedChildrenIds($childrenIds);
        return $this;
    }

    /**
     * Process category data after save category object
     *
     * @access protected
     * @param Varien_Object $object
     * @return Mageplaza_BetterBlog_Model_Resource_Category
     * @author Sam
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (substr($object->getPath(), -1) == '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->_savePath($object);
        }


        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('mageplaza_betterblog/category_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'category_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'category_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * Update path field
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Category $object
     * @return Mageplaza_BetterBlog_Model_Resource_Category
     * @author Sam
     */
    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('path' => $object->getPath()),
                array('entity_id = ?' => $object->getId())
            );
        }
        return $this;
    }

    /**
     * Get maximum position of child categories by specific tree path
     *
     * @access protected
     * @param string $path
     * @return int
     * @author Sam
     */
    protected function _getMaxPosition($path)
    {
        $adapter = $this->getReadConnection();
        $positionField = $adapter->quoteIdentifier('position');
        $level   = count(explode('/', $path));
        $bind = array(
            'c_level' => $level,
            'c_path'  => $path . '/%'
        );
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'MAX(' . $positionField . ')')
            ->where($adapter->quoteIdentifier('path') . ' LIKE :c_path')
            ->where($adapter->quoteIdentifier('level') . ' = :c_level');

        $position = $adapter->fetchOne($select, $bind);
        if (!$position) {
            $position = 0;
        }
        return $position;
    }

    /**
     * Get children categories count
     *
     * @access public
     * @param int $categoryId
     * @return int
     * @author Sam
     */
    public function getChildrenCount($categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'children_count')
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => $categoryId);
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Check if category id exist
     *
     * @access public
     * @param int $entityId
     * @return bool
     * @author Sam
     */
    public function checkId($entityId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id = :entity_id');
        $bind =  array('entity_id' => $entityId);
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Check array of categories identifiers
     *
     * @access public
     * @param array $ids
     * @return array
     * @author Sam
     */
    public function verifyIds(array $ids)
    {
        if (empty($ids)) {
            return array();
        }
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id IN(?)', $ids);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Get count of active/not active children categories
     *
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @param bool $isActiveFlag
     * @return int
     * @author Sam
     */
    public function getChildrenAmount($category, $isActiveFlag = true)
    {
        $bind = array(
            'active_flag'  => $isActiveFlag,
            'c_path'   => $category->getPath() . '/%'
        );
        $select = $this->_getReadAdapter()->select()
            ->from(array('m' => $this->getMainTable()), array('COUNT(m.entity_id)'))
            ->where('m.path LIKE :c_path')
            ->where('status' . ' = :active_flag');
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Return parent categories of category
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @return array
     * @author Sam
     */
    public function getParentCategories($category)
    {
        $pathIds = array_reverse(explode('/', $category->getPath()));
        $categories = Mage::getResourceModel('mageplaza_betterblog/category_collection')
            ->addFieldToFilter('entity_id', array('in' => $pathIds))
            ->load()
            ->getItems();
        return $categories;
    }

    /**
     * Return child categories
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Collection
     * @author Sam
     */
    public function getChildrenCategories($category)
    {
        $collection = $category->getCollection();
        $collection
            ->addIdFilter($category->getChildCategories())
            ->setOrder('position', Varien_Db_Select::SQL_ASC)
            ->load();
        return $collection;
    }
    /**
     * Return children ids of category
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @param boolean $recursive
     * @return array
     * @author Sam
     */
    public function getChildren($category, $recursive = true)
    {
        $bind = array(
            'c_path'   => $category->getPath() . '/%'
        );
        $select = $this->_getReadAdapter()->select()
            ->from(array('m' => $this->getMainTable()), 'entity_id')
            ->where('status = ?', 1)
            ->where($this->_getReadAdapter()->quoteIdentifier('path') . ' LIKE :c_path');
        if (!$recursive) {
            $select->where($this->_getReadAdapter()->quoteIdentifier('level') . ' <= :c_level');
            $bind['c_level'] = $category->getLevel() + 1;
        }
        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }

    /**
     * Process category data before saving
     * prepare path and increment children count for parent categories
     *
     * @access protected
     * @param Varien_Object $object
     * @return Mageplaza_BetterBlog_Model_Resource_Category
     * @author Sam
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getInitialSetupFlag()) {
            $urlKey = $object->getData('url_key');
            if ($urlKey == '') {
                $urlKey = $object->getName();
            }
            $urlKey = $this->formatUrlKey($urlKey);
            $validKey = false;
            while (!$validKey) {
                $entityId = $this->checkUrlKey($urlKey, $object->getStoreId(), false);
                if ($entityId == $object->getId() || empty($entityId)) {
                    $validKey = true;
                } else {
                    $parts = explode('-', $urlKey);
                    $last = $parts[count($parts) - 1];
                    if (!is_numeric($last)) {
                        $urlKey = $urlKey.'-1';
                    } else {
                        $suffix = '-'.($last + 1);
                        unset($parts[count($parts) - 1]);
                        $urlKey = implode('-', $parts).$suffix;
                    }
                }
            }
            $object->setData('url_key', $urlKey);
        }
        parent::_beforeSave($object);
        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }
        if ($object->getLevel() === null) {
            $object->setLevel(1);
        }
        if (!$object->getId() && !$object->getInitialSetupFlag()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            $path  = explode('/', $object->getPath());
            $level = count($path);
            $object->setLevel($level);
            if ($level) {
                $object->setParentId($path[$level - 1]);
            }
            $object->setPath($object->getPath() . '/');
            $toUpdateChild = explode('/', $object->getPath());
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('children_count'  => new Zend_Db_Expr('children_count+1')),
                array('entity_id IN(?)' => $toUpdateChild)
            );
        }
        return $this;
    }

    /**
     * Retrieve categories
     *
     * @access public
     * @param integer $parent
     * @param integer $recursionLevel
     * @param boolean|string $sorted
     * @param boolean $asCollection
     * @param boolean $toLoad
     * @return Varien_Data_Tree_Node_Collection|Mageplaza_BetterBlog_Model_Resource_Category_Collection
     * @author Sam
     */
    public function getCategories(
        $parent,
        $recursionLevel = 0,
        $sorted = false,
        $asCollection = false,
        $toLoad = true
    )
    {
        $tree = Mage::getResourceModel('mageplaza_betterblog/category_tree');
        $nodes = $tree->loadNode($parent)
            ->loadChildren($recursionLevel)
            ->getChildren();
        $tree->addCollectionData(null, $sorted, $parent, $toLoad, true);
        if ($asCollection) {
            return $tree->getCollection();
        }
        return $nodes;
    }

    /**
     * Return all children ids of category (with category id)
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @return array
     * @author Sam
     */
    public function getAllChildren($category)
    {
        $children = $this->getChildren($category);
        $myId = array($category->getId());
        $children = array_merge($myId, $children);
        return $children;
    }

    /**
     * Check category is forbidden to delete.
     *
     * @access public
     * @param integer $categoryId
     * @return boolean
     * @author Sam
     */
    public function isForbiddenToDelete($categoryId)
    {
        return ($categoryId == Mage::helper('mageplaza_betterblog/category')->getRootCategoryId());
    }

    /**
     * Get category path value by its id
     *
     * @access public
     * @param int $categoryId
     * @return string
     * @author Sam
     */
    public function getCategoryPathById($categoryId)
    {
        $select = $this->getReadConnection()->select()
            ->from($this->getMainTable(), array('path'))
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => (int)$categoryId);
        return $this->getReadConnection()->fetchOne($select, $bind);
    }

    /**
     * Move category to another parent node
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @param Mageplaza_BetterBlog_Model_Category $newParent
     * @param null|int $afterCategoryId
     * @return Mageplaza_BetterBlog_Model_Resource_Category
     * @author Sam
     */
    public function changeParent(
        Mageplaza_BetterBlog_Model_Category $category,
        Mageplaza_BetterBlog_Model_Category $newParent,
        $afterCategoryId = null
    )
    {
        $childrenCount  = $this->getChildrenCount($category->getId()) + 1;
        $table          = $this->getMainTable();
        $adapter        = $this->_getWriteAdapter();
        $levelFiled     = $adapter->quoteIdentifier('level');
        $pathField      = $adapter->quoteIdentifier('path');

        /**
         * Decrease children count for all old category parent categories
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count - ' . $childrenCount)),
            array('entity_id IN(?)' => $category->getParentIds())
        );
        /**
         * Increase children count for new category parents
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count + ' . $childrenCount)),
            array('entity_id IN(?)' => $newParent->getPathIds())
        );

        $position = $this->_processPositions($category, $newParent, $afterCategoryId);

        $newPath  = sprintf('%s/%s', $newParent->getPath(), $category->getId());
        $newLevel = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $category->getLevel();

        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            array(
                'path' => new Zend_Db_Expr(
                    'REPLACE(' . $pathField . ','.
                    $adapter->quote($category->getPath() . '/'). ', '.$adapter->quote($newPath . '/').')'
                ),
                'level' => new Zend_Db_Expr($levelFiled . ' + ' . $levelDisposition)
            ),
            array($pathField . ' LIKE ?' => $category->getPath() . '/%')
        );
        /**
         * Update moved category data
         */
        $data = array(
            'path'  => $newPath,
            'level' => $newLevel,
            'position'  =>$position,
            'parent_id' =>$newParent->getId()
        );
        $adapter->update($table, $data, array('entity_id = ?' => $category->getId()));
        // Update category object to new data
        $category->addData($data);
        return $this;
    }

    /**
     * Process positions of old parent category children and new parent category children.
     * Get position for moved category
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @param Mageplaza_BetterBlog_Model_Category $newParent
     * @param null|int $afterCategoryId
     * @return int
     * @author Sam
     */
    protected function _processPositions($category, $newParent, $afterCategoryId)
    {
        $table  = $this->getMainTable();
        $adapter= $this->_getWriteAdapter();
        $positionField  = $adapter->quoteIdentifier('position');

        $bind = array(
            'position' => new Zend_Db_Expr($positionField . ' - 1')
        );
        $where = array(
            'parent_id = ?' => $category->getParentId(),
            $positionField . ' > ?' => $category->getPosition()
        );
        $adapter->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterCategoryId) {
            $select = $adapter->select()
                ->from($table, 'position')
                ->where('entity_id = :entity_id');
            $position = $adapter->fetchOne($select, array('entity_id' => $afterCategoryId));
            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );
            $where = array(
                'parent_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            );
            $adapter->update($table, $bind, $where);
        } elseif ($afterCategoryId !== null) {
            $position = 0;
            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );
            $where = array(
                'parent_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            );
            $adapter->update($table, $bind, $where);
        } else {
            $select = $adapter->select()
                ->from($table, array('position' => new Zend_Db_Expr('MIN(' . $positionField. ')')))
                ->where('parent_id = :parent_id');
            $position = $adapter->fetchOne($select, array('parent_id' => $newParent->getId()));
        }
        $position += 1;
        return $position;
    }

    /**
     * check url key
     *
     * @access public
     * @param string $urlKey
     * @param int $storeId
     * @param bool $active
     * @return mixed
     * @author Sam
     */
    public function checkUrlKey($urlKey, $storeId, $active = true)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_initCheckUrlKeySelect($urlKey, $stores);
        if ($active) {
            $select->where('e.status = ?', $active);
        }
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Check for unique URL key
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     * @author Sam
     */
    public function getIsUniqueUrlKey(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode() || !$object->hasStores()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }
        $select = $this->_initCheckUrlKeySelect($object->getData('url_key'), $stores);
        if ($object->getId()) {
            $select->where('e.entity_id <> ?', $object->getId());
        }
        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    /**
     * Check if the URL key is numeric
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     * @author Sam
     */
    protected function isNumericUrlKey(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Check if the URL key is valid
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     * @author Sam
     */
    protected function isValidUrlKey(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }

    /**
     * format string as url key
     *
     * @access public
     * @param string $str
     * @return string
     * @author Sam
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }

    /**
     * init the check select
     *
     * @access protected
     * @param string $urlKey
     * @param array $store
     * @return Zend_Db_Select
     * @author Sam
     */
    protected function _initCheckUrlKeySelect($urlKey, $store)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $this->getMainTable()))
            ->join(
                array('es' => $this->getTable('mageplaza_betterblog/category_store')),
                'e.entity_id = es.category_id',
                array())
            ->where('e.url_key = ?', $urlKey)
            ->where('es.store_id IN (?)', $store);
        return $select;
    }}
