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
 * Tag abstract REST API handler model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
abstract class Mageplaza_BetterBlog_Model_Api2_Tag_Rest extends Mageplaza_BetterBlog_Model_Api2_Tag
{
    /**
     * current tag
     */
    protected $_tag;

    /**
     * retrieve entity
     *
     * @access protected
     * @return array|mixed
     * @author Sam
     */
    protected function _retrieve() {
        $tag = $this->_getTag();
        $this->_prepareTagForResponse($tag);
        return $tag->getData();
    }

    /**
     * get collection
     *
     * @access protected
     * @return array
     * @author Sam
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('mageplaza_betterblog/tag_collection');
        $entityOnlyAttributes = $this->getEntityOnlyAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ
        );
        $availableAttributes = array_keys($this->getAvailableAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        );
        $collection->addFieldToFilter('status', array('eq' => 1));
        $store = $this->_getStore();
        $collection->addStoreFilter($store->getId());
        $this->_applyCollectionModifiers($collection);
        $tags = $collection->load();
        $tags->walk('afterLoad');
        foreach ($tags as $tag) {
            $this->_setTag($tag);
            $this->_prepareTagForResponse($tag);
        }
        $tagsArray = $tags->toArray();
        $tagsArray = $tagsArray['items'];

        return $tagsArray;
    }

    /**
     * prepare tag for response
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Tag $tag
     * @author Sam
     */
    protected function _prepareTagForResponse(Mageplaza_BetterBlog_Model_Tag $tag) {
        $tagData = $tag->getData();
        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            $tagData['url'] = $tag->getTagUrl();
        }
    }

    /**
     * create tag
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
     * update tag
     *
     * @access protected
     * @param array $data
     * @author Sam
     */
    protected function _update(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete tag
     *
     * @access protected
     * @author Sam
     */
    protected function _delete() {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete current tag
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Tag $tag
     * @author Sam
     */
    protected function _setTag(Mageplaza_BetterBlog_Model_Tag $tag) {
        $this->_tag = $tag;
    }

    /**
     * get current tag
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Tag
     * @author Sam
     */
    protected function _getTag() {
        if (is_null($this->_tag)) {
            $tagId = $this->getRequest()->getParam('id');
            $tag = Mage::getModel('mageplaza_betterblog/tag');
            $tag->load($tagId);
            if (!($tag->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            if ($this->_getStore()->getId()) {
                $isValidStore = count(array_intersect(array(0, $this->_getStore()->getId()), $tag->getStoreId()));
                if (!$isValidStore) {
                    $this->_critical(self::RESOURCE_NOT_FOUND);
                }
            }
            $this->_tag = $tag;
        }
        return $this->_tag;
    }
}
