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
 * Post abstract REST API handler model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
abstract class Mageplaza_BetterBlog_Model_Api2_Post_Rest extends Mageplaza_BetterBlog_Model_Api2_Post
{
    /**
     * current post
     */
    protected $_post;

    /**
     * retrieve entity
     *
     * @access protected
     * @return array|mixed
     * @author Sam
     */
    protected function _retrieve() {
        $post = $this->_getPost();
        $this->_preparePostForResponse($post);
        return $post->getData();
    }

    /**
     * get collection
     *
     * @access protected
     * @return array
     * @author Sam
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('mageplaza_betterblog/post_collection')->addAttributeToSelect('*');
        $collection->setStoreId($this->_getStore()->getId());
        $entityOnlyAttributes = $this->getEntityOnlyAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ
        );
        $availableAttributes = array_keys($this->getAvailableAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        );
        $collection->addAttributeToFilter('status', array('eq' => 1));
        $this->_applyCollectionModifiers($collection);
        $posts = $collection->load();
        $posts->walk('afterLoad');
        foreach ($posts as $post) {
            $this->_setPost($post);
            $this->_preparePostForResponse($post);
        }
        $postsArray = $posts->toArray();
        return $postsArray;
    }

    /**
     * prepare post for response
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Post $post
     * @author Sam
     */
    protected function _preparePostForResponse(Mageplaza_BetterBlog_Model_Post $post) {
        $postData = $post->getData();
        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            $postData['url'] = $post->getPostUrl();
        }
    }

    /**
     * create post
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
     * update post
     *
     * @access protected
     * @param array $data
     * @author Sam
     */
    protected function _update(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete post
     *
     * @access protected
     * @author Sam
     */
    protected function _delete() {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete current post
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Post $post
     * @author Sam
     */
    protected function _setPost(Mageplaza_BetterBlog_Model_Post $post) {
        $this->_post = $post;
    }

    /**
     * get current post
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Post
     * @author Sam
     */
    protected function _getPost() {
        if (is_null($this->_post)) {
            $postId = $this->getRequest()->getParam('id');
            $post = Mage::getModel('mageplaza_betterblog/post');
            $storeId = $this->_getStore()->getId();
            $post->setStoreId($storeId);
            $post->load($postId);
            if (!($post->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $this->_post = $post;
        }
        return $this->_post;
    }
}
