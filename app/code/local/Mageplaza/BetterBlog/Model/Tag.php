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
 * Tag model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Tag extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'mageplaza_betterblog_tag';
    const CACHE_TAG = 'mageplaza_betterblog_tag';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_betterblog_tag';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'tag';
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
        $this->_init('mageplaza_betterblog/tag');
    }

    /**
     * before save tag
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Tag
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
     * get the url to the tag details page
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getTagUrl()
    {
        if ($this->getUrlKey()) {
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('mageplaza_betterblog/tag/url_prefix')) {
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('mageplaza_betterblog/tag/url_suffix')) {
                $urlKey .= '.'.$suffix;
            }
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        return Mage::getUrl('mageplaza_betterblog/tag/view', array('id'=>$this->getId()));
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
     * save tag relation
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Tag
     * @author Sam
     */
    protected function _afterSave()
    {
        $this->getPostInstance()->saveTagRelation($this);
        return parent::_afterSave();
    }

    /**
     * get post relation model
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Tag_Post
     * @author Sam
     */
    public function getPostInstance()
    {
        if (!$this->_postInstance) {
            $this->_postInstance = Mage::getSingleton('mageplaza_betterblog/tag_post');
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
     * @return Mageplaza_BetterBlog_Model_Tag_Post_Collection
     * @author Sam
     */
    public function getSelectedPostsCollection()
    {
        $collection = $this->getPostInstance()->getPostsCollection($this);
        return $collection;
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


    /**
     * Update tag count
     * @param null $store
     * @return $this
     */
    public function updateTagCount($tagId, $store = null)
    {
        $model = Mage::getModel('mageplaza_betterblog/post_tag')->getCollection();
        if ($store) $model->addStoreFilter($store);

        $model->addFieldToFilter('tag_id', $tagId);
        $count = $model->count();
        $this->setCount($count)->save();
        return $this;
    }

    public function loadByName($name, $store = null)
    {
        $model = Mage::getModel('mageplaza_betterblog/tag')
            ->getCollection()
            ->addFieldToFilter('name',$name);
        if($store) $model->addStoreFilter($store);

        $model->getFirstItem();

        return $model;

    }

    
}
