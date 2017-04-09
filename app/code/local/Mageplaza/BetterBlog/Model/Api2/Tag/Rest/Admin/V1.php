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
 * Tag REST API admin handler
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Api2_Tag_Rest_Admin_V1 extends Mageplaza_BetterBlog_Model_Api2_Tag_Rest
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
     * Retrieve list of tags
     *
     * @access protected
     * @return array
     * @author Sam
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('mageplaza_betterblog/tag_collection');
        $entityOnlyAttributes = $this->getEntityOnlyAttributes($this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ);
        $availableAttributes = array_keys($this->getAvailableAttributes($this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ));
        $this->_applyCollectionModifiers($collection);
        $tags = $collection->load();

        foreach ($tags as $tag) {
            $this->_setTag($tag);
            $this->_prepareTagForResponse($tag);
        }
        $tagsArray = $tags->toArray();
        $tagsArray = $tagsArray['items'];

        return $tagsArray;
    }

    /**
     * Delete tag by its ID
     *
     * @access protected
     * @throws Mage_Api2_Exception
     * @author Sam
     */
    protected function _delete() {
        $tag = $this->_getTag();
        try {
            $tag->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Create tag
     *
     * @access protected
     * @param array $data
     * @return string
     * @author Sam
     */
    protected function _create(array $data) {
        $tag = Mage::getModel('mageplaza_betterblog/tag')->setData($data);
        try {
            $tag->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
        return $this->_getLocation($tag->getId());
    }

    /**
     * Update tag by its ID
     *
     * @access protected
     * @param array $data
     * @author Sam
     */
    protected function _update(array $data) {
        $tag = $this->_getTag();
        $tag->addData($data);
        try {
            $tag->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
    }

    /**
     * Set additional data before tag save
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Tag $entity
     * @param array $tagData
     * @author Sam
     */
    protected function _prepareDataForSave($product, $productData) {
        //add your data processing algorithm here if needed
    }
}