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
class Mageplaza_BetterBlog_Model_Category_Api extends Mage_Api_Model_Resource_Abstract
{


    /**
     * init category
     *
     * @access protected
     * @param $categoryId
     * @return Mageplaza_BetterBlog_Model_Category
     * @author      Sam
     */
    protected function _initCategory($categoryId)
    {
        $category = Mage::getModel('mageplaza_betterblog/category')->load($categoryId);
        if (!$category->getId()) {
            $this->_fault('category_not_exists');
        }
        return $category;
    }

    /**
     * get categories
     *
     * @access public
     * @param mixed $filters
     * @return array
     * @author Sam
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('mageplaza_betterblog/category')->getCollection()
            ->addFieldToFilter(
                'entity_id',
                array(
                    'neq'=>Mage::helper('mageplaza_betterblog/category')->getRootCategoryId()
                )
            );
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
        foreach ($collection as $category) {
            $result[] = $this->_getApiData($category);
        }
        return $result;
    }

    /**
     * Add category
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
            $category = Mage::getModel('mageplaza_betterblog/category')
                ->setData((array)$data)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $category->getId();
    }

    /**
     * Change existing category information
     *
     * @access public
     * @param int $categoryId
     * @param array $data
     * @return bool
     * @author Sam
     */
    public function update($categoryId, $data)
    {
        $category = $this->_initCategory($categoryId);
        try {
            $data = (array)$data;
            $category->addData($data);
            $category->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * remove category
     *
     * @access public
     * @param int $categoryId
     * @return bool
     * @author Sam
     */
    public function remove($categoryId)
    {
        $category = $this->_initCategory($categoryId);
        try {
            $category->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }
        return true;
    }

    /**
     * get info
     *
     * @access public
     * @param int $categoryId
     * @return array
     * @author Sam
     */
    public function info($categoryId)
    {
        $result = array();
        $category = $this->_initCategory($categoryId);
        $result = $this->_getApiData($category);
        //related posts
        $result['posts'] = array();
        $relatedPostsCollection = $category->getSelectedPostsCollection();
        foreach ($relatedPostsCollection as $post) {
            $result['posts'][$post->getId()] = $post->getPosition();
        }
        return $result;
    }

    /**
     * Move category in tree
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return boolean
     */
    public function move($categoryId, $parentId, $afterId = null)
    {
        $category = $this->_initCategory($categoryId);
        $parentCategory = $this->_initCategory($parentId);
        if ($afterId === null && $parentCategory->hasChildren()) {
            $parentChildren = $parentCategory->getChildCategories();
            $afterId = array_pop(explode(',', $parentChildren));
        }
        if ( strpos($parentCategory->getPath(), $category->getPath()) === 0) {
            $this->_fault(
                'not_moved',
                Mage::helper('mageplaza_betterblog')->__("Cannot move parent inside category")
            );
        }
        try {
            $category->move($parentId, $afterId);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_moved', $e->getMessage());
        }
        return true;
    }

    /**
     * Assign post to category
     *
     * @access public
     * @param int $categoryId
     * @param int $postId
     * @param int $position
     * @return boolean
     * @author Sam
     */
    public function assignPost($categoryId, $postId, $position = null)
    {
        $category = $this->_initCategory($categoryId);
        $positions    = array();
        $posts     = $category->getSelectedPosts();
        foreach ($posts as $post) {
            $posts[$post->getId()] = array('position'=>$post->getPosition());
        }
        $post = Mage::getModel('mageplaza_betterblog/post')->load($postId);
        if (!$post->getId()) {
            $this->_fault('category_post_not_exists');
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
     * remove post from category
     *
     * @access public
     * @param int $categoryId
     * @param int $postId
     * @return boolean
     * @author Sam
     */
    public function unassignPost($categoryId, $postId)
    {
        $category = $this->_initCategory($categoryId);
        $positions    = array();
        $posts     = $category->getSelectedPosts();
        foreach ($posts as $post) {
            $posts[$post->getId()] = array('position'=>$post->getPosition());
        }
        unset($positions[$postId]);
        $category->setPostsData($positions);
        try {
            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * get data for api
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Category $category
     * @return array()
     * @author Sam
     */
    protected function _getApiData(Mageplaza_BetterBlog_Model_Category $category)
    {
        return $category->getData();
    }
}
