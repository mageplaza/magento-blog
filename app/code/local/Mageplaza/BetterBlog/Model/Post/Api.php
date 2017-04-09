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
class Mageplaza_BetterBlog_Model_Post_Api extends Mage_Api_Model_Resource_Abstract
{
    protected $_defaultAttributeList = array(
        'post_title', 
        'post_excerpt', 
        'post_content', 
        'image', 
        'status', 
        'url_key', 
        'in_rss', 
        'meta_title', 
        'meta_keywords', 
        'meta_description', 
        'allow_comment', 
        'created_at', 
        'updated_at', 
    );


    /**
     * init post
     *
     * @access protected
     * @param $postId
     * @return Mageplaza_BetterBlog_Model_Post
     * @author      Sam
     */
    protected function _initPost($postId)
    {
        $post = Mage::getModel('mageplaza_betterblog/post')->load($postId);
        if (!$post->getId()) {
            $this->_fault('post_not_exists');
        }
        return $post;
    }

    /**
     * get posts
     *
     * @access public
     * @param mixed $filters
     * @return array
     * @author Sam
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('mageplaza_betterblog/post')->getCollection()
            ->addAttributeToSelect('*');
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
        foreach ($collection as $post) {
            $result[] = $this->_getApiData($post);
        }
        return $result;
    }

    /**
     * Add post
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
            if (isset($data['additional_attributes']) && is_array($data['additional_attributes'])) {
                foreach ($data['additional_attributes'] as $key=>$value) {
                    $data[$key] = $value;
                }
                unset($data['additional_attributes']);
            }
            $data['attribute_set_id'] = Mage::getModel('mageplaza_betterblog/post')->getDefaultAttributeSetId();
            $post = Mage::getModel('mageplaza_betterblog/post')
                ->setData((array)$data)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $post->getId();
    }

    /**
     * Change existing post information
     *
     * @access public
     * @param int $postId
     * @param array $data
     * @return bool
     * @author Sam
     */
    public function update($postId, $data)
    {
        $post = $this->_initPost($postId);
        try {
            $data = (array)$data;
            if (isset($data['additional_attributes']) && is_array($data['additional_attributes'])) {
                foreach ($data['additional_attributes'] as $key=>$value) {
                    $data[$key] = $value;
                }
                unset($data['additional_attributes']);
            }
            $post->addData($data);
            $post->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * remove post
     *
     * @access public
     * @param int $postId
     * @return bool
     * @author Sam
     */
    public function remove($postId)
    {
        $post = $this->_initPost($postId);
        try {
            $post->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }
        return true;
    }

    /**
     * get info
     *
     * @access public
     * @param int $postId
     * @return array
     * @author Sam
     */
    public function info($postId)
    {
        $result = array();
        $post = $this->_initPost($postId);
        $result = $this->_getApiData($post);
        //related categories
        $result['categories'] = array();
        $relatedCategoriesCollection = $post->getSelectedCategoriesCollection();
        foreach ($relatedCategoriesCollection as $category) {
            $result['categories'][$category->getId()] = $category->getPosition();
        }
        //related tags
        $result['tags'] = array();
        $relatedTagsCollection = $post->getSelectedTagsCollection();
        foreach ($relatedTagsCollection as $tag) {
            $result['tags'][$tag->getId()] = $tag->getPosition();
        }
        return $result;
    }

    /**
     * Assign tag to post
     *
     * @access public
     * @param int $postId
     * @param int $tagId
     * @param int $position
     * @return boolean
     * @author Sam
     */
    public function assignTag($postId, $tagId, $position = null)
    {
        $post = $this->_initPost($postId);
        $positions    = array();
        $tags     = $post->getSelectedTags();
        foreach ($tags as $tag) {
            $tags[$tag->getId()] = array('position'=>$tag->getPosition());
        }
        $tag = Mage::getModel('mageplaza_betterblog/tag')->load($tagId);
        if (!$tag->getId()) {
            $this->_fault('post_tag_not_exists');
        }
        $positions[$tagId]['position'] = $position;
        $tag->setTagsData($positions);
        try {
            $tag->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove tag from post
     *
     * @access public
     * @param int $postId
     * @param int $tagId
     * @return boolean
     * @author Sam
     */
    public function unassignTag($postId, $tagId)
    {
        $post = $this->_initPost($postId);
        $positions    = array();
        $tags     = $post->getSelectedTags();
        foreach ($tags as $tag) {
            $tags[$tag->getId()] = array('position'=>$tag->getPosition());
        }
        unset($positions[$tagId]);
        $post->setTagsData($positions);
        try {
            $post->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Assign category to post
     *
     * @access public
     * @param int $postId
     * @param int $categoryId
     * @return boolean
     * @author Sam
     */
    public function assignCategory($postId, $categoryId)
    {
        $post = $this->_initPost($postId);
        $category   = Mage::getModel('mageplaza_betterblog/category')->load($categoryId);
        if (!$category->getId()) {
            $this->_fault('category_not_exists');
        }
        $categories = $post->getSelectedCategories();
        $categoryIds = array();
        foreach ($categories as $category) {
            $categoryIds[] = $category->getId();
        }
        $categoryIds[] = $categoryId;
        $post->setCategoriesData($categoryIds);
        try {
            $post->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove category from post
     *
     * @access public
     * @param int $postId
     * @param int $categoryId
     * @return boolean
     * @author Sam
     */
    public function unassignCategory($postId, $categoryId)
    {
        $post      = $this->_initPost($postId);
        $categories    = $post->getSelectedCategories();
        $categoryIds  = array();
        foreach ($categories as $key=>$category) {
            if ($category->getId() != $categoryId) {
                $categoryIds[] = $category->getId();
            }
        }
        $post->setCategoriesData($categoryIds);
        try {
            $post->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Get list of additional attributes which are not in default create/update list
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getAdditionalAttributes()
    {
        $entity = Mage::getModel('eav/entity_type')->load('mageplaza_betterblog_post', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId());
        $result = array();
        foreach ($attributes as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $this->_defaultAttributeList)) {
                if ($attribute->getIsGlobal() == Mageplaza_BetterBlog_Model_Attribute::SCOPE_GLOBAL) {
                    $scope = 'global';
                } elseif ($attribute->getIsGlobal() == Mageplaza_BetterBlog_Model_Attribute::SCOPE_WEBSITE) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'attribute_id' => $attribute->getId(),
                    'code'         => $attribute->getAttributeCode(),
                    'type'         => $attribute->getFrontendInput(),
                    'required'     => $attribute->getIsRequired(),
                    'scope'        => $scope
                );
            }
        }

        return $result;
    }

    /**
     * get data for api
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Post $post
     * @return array()
     * @author Sam
     */
    protected function _getApiData(Mageplaza_BetterBlog_Model_Post $post)
    {
        $data = array();
        $additional = array();
        $additionalAttributes = $this->getAdditionalAttributes();
        $additionalByCode = array();
        foreach ($additionalAttributes as $attribute) {
            $additionalByCode[] = $attribute['code'];
        }
        foreach ($post->getData() as $key=>$value) {
            if (!in_array($key, $additionalByCode)) {
                $data[$key] = $value;
            } else {
                $additional[] = array('key'=>$key, 'value'=>$value);
            }
        }
        if (!empty($additional)) {
            $data['additional_attributes'] = $additional;
        }
        return $data;
    }
}
