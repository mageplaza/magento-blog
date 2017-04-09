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
 * Post model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Post extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'mageplaza_betterblog_post';
    const CACHE_TAG = 'mageplaza_betterblog_post';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_betterblog_post';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'post';
    protected $_categoryInstance = null;
    protected $_tagInstance = null;

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
        $this->_init('mageplaza_betterblog/post');
    }

    /**
     * before save post
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Post
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
     * get the url to the post details page
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getPostUrl()
    {
        if ($this->getUrlKey()) {
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('mageplaza_betterblog/post/url_prefix')) {
                $urlKey .= $prefix . '/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('mageplaza_betterblog/post/url_suffix')) {
                $urlKey .= '.' . $suffix;
            }
            return Mage::getUrl('', array('_direct' => $urlKey));
        }
        return Mage::getUrl('mageplaza_betterblog/post/view', array('id' => $this->getId()));
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
     * get the post Content
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getPostContent()
    {
        $post_content = $this->getData('post_content');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($post_content);
        return $html;
    }

    /**
     * save post relation
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Post
     * @author Sam
     */
    protected function _afterSave()
    {
        $this->getCategoryInstance()->savePostRelation($this);
        $this->getTagInstance()->savePostRelation($this);
        return parent::_afterSave();
    }

    /**
     * get category relation model
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Post_Category
     * @author Sam
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('mageplaza_betterblog/post_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected  array
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Post_Category_Collection
     * @author Sam
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoriesCollection($this);
        return $collection;
    }

    /**
     * get tag relation model
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Post_Tag
     * @author Sam
     */
    public function getTagInstance()
    {
        if (!$this->_tagInstance) {
            $this->_tagInstance = Mage::getSingleton('mageplaza_betterblog/post_tag');
        }
        return $this->_tagInstance;
    }

    /**
     * get selected  array
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getSelectedTags()
    {
        if (!$this->hasSelectedTags()) {
            $tags = array();
            foreach ($this->getSelectedTagsCollection() as $tag) {
                $tags[] = $tag;
            }
            $this->setSelectedTags($tags);
        }
        return $this->getData('selected_tags');
    }

    /**
     * Retrieve collection selected
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Post_Tag_Collection
     * @author Sam
     */
    public function getSelectedTagsCollection()
    {
        $collection = $this->getTagInstance()->getTagsCollection($this);
        return $collection;
    }

    /**
     * Retrieve default attribute set id
     *
     * @access public
     * @return int
     * @author Sam
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * get attribute text value
     *
     * @access public
     * @param $attributeCode
     * @return string
     * @author Sam
     */
    public function getAttributeText($attributeCode)
    {
        $text = $this->getResource()
            ->getAttribute($attributeCode)
            ->getSource()
            ->getOptionText($this->getData($attributeCode));
        if (is_array($text)) {
            return implode(', ', $text);
        }
        return $text;
    }

    /**
     * check if comments are allowed
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getAllowComments()
    {
        if ($this->getData('allow_comment') == Mageplaza_BetterBlog_Model_Adminhtml_Source_Yesnodefault::NO) {
            return false;
        }
        if ($this->getData('allow_comment') == Mageplaza_BetterBlog_Model_Adminhtml_Source_Yesnodefault::YES) {
            return true;
        }
        return Mage::getStoreConfigFlag('mageplaza_betterblog/post/allow_comment');
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
        $values['allow_comment'] = Mageplaza_BetterBlog_Model_Adminhtml_Source_Yesnodefault::USE_DEFAULT;
        return $values;
    }


    /**
     * Update view count
     */
    public function updateViewCount()
    {
        try {
            $this->setViews($this->getViews() + 1);
            $this->save();
        } catch (Exception $e) {
        }
    }

    /**
     * get category as html list
     * @param $post
     * @return null|string
     */
    public function getPostCategoryHtml()
    {
        $categories = $this->getSelectedCategories();
        if (!$categories) return null;
        $categoryHtml = array();

        foreach ($categories as $_cat) {
            $categoryHtml[] = '<a href="' . $_cat->getCategoryUrl() . '">' . $_cat->getName() . '</a>';
        }

        $result = implode(', ', $categoryHtml);
        return $result;

    }

    public function getPostsSameTopic()
    {
        $topic = $this->getTopics();
        if (!$topic) return null;

        $config = Mage::helper('mageplaza_betterblog/config');
        $collection = Mage::getResourceModel('mageplaza_betterblog/post_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('topics', $topic)
            ->addAttributeToFilter('entity_id', array('neq' => $this->getId()))
            ->addAttributeToFilter('status', 1);
        $collection->setOrder('created_at', 'desc');
        $count = $this->getPostCount() ? $this->getPostCount() : $config->getPostConfig('number_recent_posts');
        $collection->setPageSize($count);

        return $collection;
    }

    public function getTopicLabel()
    {
        return $this->getResource()
            ->getAttribute('topics')
            ->getFrontend()
            ->getOption($this->getTopics());
    }


}
