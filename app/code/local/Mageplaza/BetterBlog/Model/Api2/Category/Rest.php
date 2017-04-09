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
 * Category abstract REST API handler model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
abstract class Mageplaza_BetterBlog_Model_Api2_Category_Rest extends Mageplaza_BetterBlog_Model_Api2_Category
{
    /**
     * current category
     */
    protected $_category;

    /**
     * retrieve entity
     *
     * @access protected
     * @return array|mixed
     * @author Sam
     */
    protected function _retrieve() {
        $category = $this->_getCategory();
        $this->_prepareCategoryForResponse($category);
        return $category->getData();
    }

    /**
     * get collection
     *
     * @access protected
     * @return array
     * @author Sam
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('mageplaza_betterblog/category_collection');
        $entityOnlyAttributes = $this->getEntityOnlyAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ
        );
        $availableAttributes = array_keys($this->getAvailableAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        );
        $collection->addFieldToFilter('status', array('eq' => 1));
        $collection->addFieldToFilter('entity_id', array('neq'=>Mage::helper('mageplaza_betterblog/category')->getRootCategoryId()));
        $store = $this->_getStore();
        $collection->addStoreFilter($store->getId());
        $this->_applyCollectionModifiers($collection);
        $categories = $collection->load();
        $categories->walk('afterLoad');
        foreach ($categories as $category) {
            $this->_setCategory($category);
            $this->_prepareCategoryForResponse($category);
        }
        $categoriesArray = $categories->toArray();
        $categoriesArray = $categoriesArray['items'];

        return $categoriesArray;
    }

    /**
     * prepare category for response
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @author Sam
     */
    protected function _prepareCategoryForResponse(Mageplaza_BetterBlog_Model_Category $category) {
        $categoryData = $category->getData();
        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            $categoryData['url'] = $category->getCategoryUrl();
        }
    }

    /**
     * create category
     *
     * @access protected
     * @param array $data
     * @return string|void
     * @author Sam
     */
    protected function _create(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * update category
     *
     * @access protected
     * @param array $data
     * @author Sam
     */
    protected function _update(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete category
     *
     * @access protected
     * @author Sam
     */
    protected function _delete() {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete current category
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @author Sam
     */
    protected function _setCategory(Mageplaza_BetterBlog_Model_Category $category) {
        $this->_category = $category;
    }

    /**
     * get current category
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Category
     * @author Sam
     */
    protected function _getCategory() {
        if (is_null($this->_category)) {
            $categoryId = $this->getRequest()->getParam('id');
            $category = Mage::getModel('mageplaza_betterblog/category');
            $category->load($categoryId);
            if (!($category->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            if ($this->_getStore()->getId()) {
                $isValidStore = count(array_intersect(array(0, $this->_getStore()->getId()), $category->getStoreId()));
                if (!$isValidStore) {
                    $this->_critical(self::RESOURCE_NOT_FOUND);
                }
            }
            $this->_category = $category;
        }
        return $this->_category;
    }
}
