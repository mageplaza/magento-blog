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
 * Post comment model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Post_Comment extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'mageplaza_betterblog_post_comment';
    const CACHE_TAG = 'mageplaza_betterblog_post_comment';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_betterblog_post_comment';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'comment';

    protected $_post;
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
        $this->_init('mageplaza_betterblog/post_comment');
    }

    /**
     * before save post comment
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Post_Comment
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
     * after save post comment
     *
     */
    protected function _afterSave()
    {
        $this->updatePostCommentCount();
        parent::_afterSave();
    }

    /**
     * process after delete comment
     */
    protected function _afterDelete()
    {
        $this->updatePostCommentCount();
        parent::_afterDelete();

    }

    /**
     * validate comment
     *
     * @access public
     * @return array|bool
     * @author Sam
     */
    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Comment title can\'t be empty');
        }

        if (!Zend_Validate::is($this->getName(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Your name can\'t be empty');
        }

        if (!Zend_Validate::is($this->getComment(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Comment can\'t be empty');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    protected function _getPost()
    {
        if($this->getPost()){
            return $this->getPOst();
        } else{
            $postId = $this->getPostId();
            $post = Mage::getModel('mageplaza_betterblog/post')->load($postId);
            $this->setPost($post);
            return $post;
        }

    }
    /**
     * update comment count
     */
    public function updatePostCommentCount()
    {
        $postId = $this->getPostId();
        $post = $this->_getPost();
        if ($post && $post->getId()) {
            $count = Mage::getModel('mageplaza_betterblog/post_comment')->getCollection()
                ->addFieldToFilter('post_id', $postId)
                ->addFieldToFilter('status', self::STATUS_APPROVED)
                ->count();
            try {
                $post->setData('comment_count',(int) $count);
                $post->save();
            } catch (Exception $e) {
                Mage::log('Betterblog: Cannot save comment count. ' . $e->getMessage());
            }
        }
    }

    public function getPostUrl()
    {
        $post = $this->_getPost();
        return $post->getPostUrl();
    }
}
