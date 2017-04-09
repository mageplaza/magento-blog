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
class Mageplaza_BetterBlog_Model_Tag_Api extends Mage_Api_Model_Resource_Abstract
{


    /**
     * init tag
     *
     * @access protected
     * @param $tagId
     * @return Mageplaza_BetterBlog_Model_Tag
     * @author      Sam
     */
    protected function _initTag($tagId)
    {
        $tag = Mage::getModel('mageplaza_betterblog/tag')->load($tagId);
        if (!$tag->getId()) {
            $this->_fault('tag_not_exists');
        }
        return $tag;
    }

    /**
     * get tags
     *
     * @access public
     * @param mixed $filters
     * @return array
     * @author Sam
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('mageplaza_betterblog/tag')->getCollection();
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $tag) {
            $result[] = $this->_getApiData($tag);
        }
        return $result;
    }

    /**
     * Add tag
     *
     * @access public
     * @param array $data
     * @return array
     * @author Sam
     */
    public function add($data)
    {
        try {
            if (is_null($data)) {
                throw new Exception(Mage::helper('mageplaza_betterblog')->__("Data cannot be null"));
            }
            $data = (array)$data;
            $tag = Mage::getModel('mageplaza_betterblog/tag')
                ->setData((array)$data)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $tag->getId();
    }

    /**
     * Change existing tag information
     *
     * @access public
     * @param int $tagId
     * @param array $data
     * @return bool
     * @author Sam
     */
    public function update($tagId, $data)
    {
        $tag = $this->_initTag($tagId);
        try {
            $data = (array)$data;
            $tag->addData($data);
            $tag->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * remove tag
     *
     * @access public
     * @param int $tagId
     * @return bool
     * @author Sam
     */
    public function remove($tagId)
    {
        $tag = $this->_initTag($tagId);
        try {
            $tag->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }
        return true;
    }

    /**
     * get info
     *
     * @access public
     * @param int $tagId
     * @return array
     * @author Sam
     */
    public function info($tagId)
    {
        $result = array();
        $tag = $this->_initTag($tagId);
        $result = $this->_getApiData($tag);
        //related posts
        $result['posts'] = array();
        $relatedPostsCollection = $tag->getSelectedPostsCollection();
        foreach ($relatedPostsCollection as $post) {
            $result['posts'][$post->getId()] = $post->getPosition();
        }
        return $result;
    }

    /**
     * Assign post to tag
     *
     * @access public
     * @param int $tagId
     * @param int $postId
     * @param int $position
     * @return boolean
     * @author Sam
     */
    public function assignPost($tagId, $postId, $position = null)
    {
        $tag = $this->_initTag($tagId);
        $positions    = array();
        $posts     = $tag->getSelectedPosts();
        foreach ($posts as $post) {
            $posts[$post->getId()] = array('position'=>$post->getPosition());
        }
        $post = Mage::getModel('mageplaza_betterblog/post')->load($postId);
        if (!$post->getId()) {
            $this->_fault('tag_post_not_exists');
        }
        $positions[$postId]['position'] = $position;
        $post->setPostsData($positions);
        try {
            $post->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove post from tag
     *
     * @access public
     * @param int $tagId
     * @param int $postId
     * @return boolean
     * @author Sam
     */
    public function unassignPost($tagId, $postId)
    {
        $tag = $this->_initTag($tagId);
        $positions    = array();
        $posts     = $tag->getSelectedPosts();
        foreach ($posts as $post) {
            $posts[$post->getId()] = array('position'=>$post->getPosition());
        }
        unset($positions[$postId]);
        $tag->setPostsData($positions);
        try {
            $tag->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * get data for api
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Tag $tag
     * @return array()
     * @author Sam
     */
    protected function _getApiData(Mageplaza_BetterBlog_Model_Tag $tag)
    {
        return $tag->getData();
    }
}
