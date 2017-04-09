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
 * Category model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Category extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'mageplaza_betterblog_category';
    const CACHE_TAG = 'mageplaza_betterblog_category';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_betterblog_category';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'category';
    protected $_postInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('mageplaza_betterblog/category');
    }

    /**
     * before save category
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Category
     * @author Sam
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get the url to the category details page
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getCategoryUrl()
    {
        if ($this->getUrlKey()) {
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('mageplaza_betterblog/category/url_prefix')) {
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('mageplaza_betterblog/category/url_suffix')) {
                $urlKey .= '.'.$suffix;
            }
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        return Mage::getUrl('mageplaza_betterblog/category/view', array('id'=>$this->getId()));
    }

    /**
     * check URL key
     *
     * @access public
     * @param string $urlKey
     * @param bool $active
     * @return mixed
     * @author Sam
     */
    public function checkUrlKey($urlKey, $active = true)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $active);
    }

    /**
     * save category relation
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Category
     * @author Sam
     */
    protected function _afterSave()
    {
        $this->getPostInstance()->saveCategoryRelation($this);
        return parent::_afterSave();
    }

    /**
     * get post relation model
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Category_Post
     * @author Sam
     */
    public function getPostInstance()
    {
        if (!$this->_postInstance) {
            $this->_postInstance = Mage::getSingleton('mageplaza_betterblog/category_post');
        }
        return $this->_postInstance;
    }

    /**
     * get selected  array
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getSelectedPosts()
    {
        if (!$this->hasSelectedPosts()) {
            $posts = array();
            foreach ($this->getSelectedPostsCollection() as $post) {
                $posts[] = $post;
            }
            $this->setSelectedPosts($posts);
        }
        return $this->getData('selected_posts');
    }

    /**
     * Retrieve collection selected
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Category_Post_Collection
     * @author Sam
     */
    public function getSelectedPostsCollection()
    {
        $collection = $this->getPostInstance()->getPostsCollection($this);
        return $collection;
    }

    /**
     * get the tree model
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Tree
     * @author Sam
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('mageplaza_betterblog/category_tree');
    }

    /**
     * get tree model instance
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Resource_Category_Tree
     * @author Sam
     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('mageplaza_betterblog/category_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move category
     *
     * @access public
     * @param   int $parentId new parent category id
     * @param   int $afterCategoryId category id after which we have put current category
     * @return  Mageplaza_BetterBlog_Model_Category
     * @author Sam
     */
    public function move($parentId, $afterCategoryId)
    {
        $parent = Mage::getModel('mageplaza_betterblog/category')->load($parentId);
        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('mageplaza_betterblog')->__(
                    'Category move operation is not possible: the new parent category was not found.'
                )
            );
        }
        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('mageplaza_betterblog')->__(
                    'Category move operation is not possible: the current category was not found.'
                )
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('mageplaza_betterblog')->__(
                    'Category move operation is not possible: parent category is equal to child category.'
                )
            );
        }
        $this->setMovedCategoryId($this->getId());
        $eventParams = array(
            $this->_eventObject => $this,
            'parent'            => $parent,
            'category_id'     => $this->getId(),
            'prev_parent_id'    => $this->getParentId(),
            'parent_id'         => $parentId
        );
        $moveComplete = false;
        $this->_getResource()->beginTransaction();
        try {
            $this->getResource()->changeParent($this, $parent, $afterCategoryId);
            $this->_getResource()->commit();
            $this->setAffectedCategoryIds(array($this->getId(), $this->getParentId(), $parentId));
            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        if ($moveComplete) {
            Mage::app()->cleanCache(array(self::CACHE_TAG));
        }
        return $this;
    }

    /**
     * Get the parent category
     *
     * @access public
     * @return  Mageplaza_BetterBlog_Model_Category
     * @author Sam
     */
    public function getParentCategory()
    {
        if (!$this->hasData('parent_category')) {
            $this->setData(
                'parent_category',
                Mage::getModel('mageplaza_betterblog/category')->load($this->getParentId())
            );
        }
        return $this->_getData('parent_category');
    }

    /**
     * Get the parent id
     *
     * @access public
     * @return  int
     * @author Sam
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Get all parent categories ids
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * Get all categories children
     *
     * @access public
     * @param bool $asArray
     * @return mixed (array|string)
     * @author Sam
     */
    public function getAllChildren($asArray = false)
    {
        $children = $this->getResource()->getAllChildren($this);
        if ($asArray) {
            return $children;
        } else {
            return implode(',', $children);
        }
    }

    /**
     * Get all categories children
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getChildCategories()
    {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * check the id
     *
     * @access public
     * @param int $id
     * @return bool
     * @author Sam
     */
    public function checkId($id)
    {
        return $this->_getResource()->checkId($id);
    }

    /**
     * Get array categories ids which are part of category path
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve level
     *
     * @access public
     * @return int
     * @author Sam
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    /**
     * Verify category ids
     *
     * @access public
     * @param array $ids
     * @return bool
     * @author Sam
     */
    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    /**
     * check if category has children
     *
     * @access public
     * @return bool
     * @author Sam
     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * check if category can be deleted
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Category
     * @author Sam
     */
    protected function _beforeDelete()
    {
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException(Mage::helper('mageplaza_betterblog')->__("Can't delete root category."));
        }
        return parent::_beforeDelete();
    }

    /**
     * get the categories
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @author Sam
     */
    public function getCategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
    {
        return $this->getResource()->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
    }

    /**
     * Return parent categories of current category
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getParentCategories()
    {
        return $this->getResource()->getParentCategories($this);
    }

    /**
     * Return children categories of current category
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getChildrenCategories()
    {
        return $this->getResource()->getChildrenCategories($this);
    }

    /**
     * check if parents are enabled
     *
     * @access public
     * @return bool
     * @author Sam
     */
    public function getStatusPath()
    {
        $parents = $this->getParentCategories();
        $rootId = Mage::helper('mageplaza_betterblog/category')->getRootCategoryId();
        foreach ($parents as $parent) {
            if ($parent->getId() == $rootId) {
                continue;
            }
            if (!$parent->getStatus()) {
                return false;
            }
        }
        return $this->getStatus();
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        $values['in_rss'] = 1;
        return $values;
    }

}
