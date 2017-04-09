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
 * Category admin block abstract
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Category_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * get current category
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Entity
     * @author Sam
     */
    public function getCategory()
    {
        return Mage::registry('category');
    }

    /**
     * get current category id
     *
     * @access public
     * @return int
     * @author Sam
     */
    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
        return null;
    }

    /**
     * get current category Name
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getCategoryName()
    {
        return $this->getCategory()->getName();
    }

    /**
     * get current category path
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getCategoryPath()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getPath();
        }
        return Mage::helper('mageplaza_betterblog/category')->getRootCategoryId();
    }

    /**
     * check if there is a root category
     *
     * @access public
     * @return bool
     * @author Sam
     */
    public function hasRootCategory()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }
        return false;
    }

    /**
     * get the root
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category|null $parentNodeCategory
     * @param int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Sam
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $rootId = Mage::helper('mageplaza_betterblog/category')->getRootCategoryId();
            $tree = Mage::getResourceSingleton('mageplaza_betterblog/category_tree')
                ->load(null, $recursionLevel);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('mageplaza_betterblog/category')->getRootCategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('mageplaza_betterblog/category')->getRootCategoryId()) {
                $root->setName(Mage::helper('mageplaza_betterblog')->__('Root'));
            }
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get and register categories root by specified categories IDs
     *
     * @accsess public
     * @param array $ids
     * @return Varien_Data_Tree_Node
     * @author Sam
     */
    public function getRootByIds($ids)
    {
        $root = Mage::registry('root');
        if (null === $root) {
            $categoryTreeResource = Mage::getResourceSingleton('mageplaza_betterblog/category_tree');
            $ids     = $categoryTreeResource->getExistingCategoryIdsBySpecifiedIds($ids);
            $tree   = $categoryTreeResource->loadByIds($ids);
            $rootId = Mage::helper('mageplaza_betterblog/category')->getRootCategoryId();
            $root   = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('mageplaza_betterblog/category')->getRootCategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('mageplaza_betterblog/category')->getRootCategoryId()) {
                $root->setName(Mage::helper('mageplaza_betterblog')->__('Root'));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * get specific node
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category $parentNodeCategory
     * @param $int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Sam
     */
    public function getNode($parentNodeCategory, $recursionLevel = 2)
    {
        $tree = Mage::getResourceModel('mageplaza_betterblog/category_tree');
        $nodeId     = $parentNodeCategory->getId();
        $parentId   = $parentNodeCategory->getParentId();
        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);
        if ($node && $nodeId != Mage::helper('mageplaza_betterblog/category')->getRootCategoryId()) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == Mage::helper('mageplaza_betterblog/category')->getRootCategoryId()) {
            $node->setName(Mage::helper('mageplaza_betterblog')->__('Root'));
        }
        $tree->addCollectionData($this->getCategoryCollection());
        return $node;
    }

    /**
     * get url for saving data
     *
     * @access public
     * @param array $args
     * @return string
     * @author Sam
     */
    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * get url for edit
     *
     * @access public
     * @param array $args
     * @return string
     * @author Sam
     */
    public function getEditUrl()
    {
        return $this->getUrl(
            "*/betterblog_category/edit",
            array('_current' => true, '_query'=>false, 'id' => null, 'parent' => null)
        );
    }

    /**
     * Return root ids
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getRootIds()
    {
        return array(Mage::helper('mageplaza_betterblog/category')->getRootCategoryId());
    }
}
