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
 * Post REST API admin handler
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Api2_Post_Rest_Admin_V1 extends Mageplaza_BetterBlog_Model_Api2_Post_Rest
{

    /**
     * Remove specified keys from associative or indexed array
     *
     * @access protected
     * @param array $array
     * @param array $keys
     * @param bool $dropOrigKeys if true - return array as indexed array
     * @return array
     * @author Sam
     */
    protected function _filterOutArrayKeys(array $array, array $keys, $dropOrigKeys = false) {
        $isIndexedArray = is_array(reset($array));
        if ($isIndexedArray) {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    $value = array_diff_key($value, array_flip($keys));
                }
            }
            if ($dropOrigKeys) {
                $array = array_values($array);
            }
            unset($value);
        } else {
            $array = array_diff_key($array, array_flip($keys));
        }
        return $array;
    }

    /**
     * Retrieve list of posts
     *
     * @access protected
     * @return array
     * @author Sam
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('mageplaza_betterblog/post_collection')->addAttributeToSelect('*');
        $collection->setStoreId($this->_getStore()->getId());
        $entityOnlyAttributes = $this->getEntityOnlyAttributes($this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ);
        $availableAttributes = array_keys($this->getAvailableAttributes($this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ));
        $this->_applyCollectionModifiers($collection);
        $posts = $collection->load();

        foreach ($posts as $post) {
            $this->_setPost($post);
            $this->_preparePostForResponse($post);
        }
        $postsArray = $posts->toArray();
        return $postsArray;
    }

    /**
     * Delete post by its ID
     *
     * @access protected
     * @throws Mage_Api2_Exception
     * @author Sam
     */
    protected function _delete() {
        $post = $this->_getPost();
        try {
            $post->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Create post
     *
     * @access protected
     * @param array $data
     * @return string
     * @author Sam
     */
    protected function _create(array $data) {
        $post = Mage::getModel('mageplaza_betterblog/post')->setData($data);
        try {
            $post->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
        return $this->_getLocation($post->getId());
    }

    /**
     * Update post by its ID
     *
     * @access protected
     * @param array $data
     * @author Sam
     */
    protected function _update(array $data) {
        $post = $this->_getPost();
        $post->addData($data);
        try {
            $post->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
    }

    /**
     * Set additional data before post save
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Post $entity
     * @param array $postData
     * @author Sam
     */
    protected function _prepareDataForSave($product, $productData) {
        //add your data processing algorithm here if needed
    }
}