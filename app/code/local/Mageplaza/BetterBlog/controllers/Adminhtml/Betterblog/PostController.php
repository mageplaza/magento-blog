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
 * Post admin controller
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Adminhtml_Betterblog_PostController extends Mage_Adminhtml_Controller_Action
{
    /**
     * constructor - set the used module name
     *
     * @access protected
     * @return void
     * @see Mage_Core_Controller_Varien_Action::_construct()
     * @author Sam
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mageplaza_BetterBlog');
    }

    /**
     * init the post
     *
     * @access protected 
     * @return Mageplaza_BetterBlog_Model_Post
     * @author Sam
     */
    protected function _initPost()
    {
        $this->_title($this->__('Better Blog'))
             ->_title($this->__('Manage Posts'));

        $postId  = (int) $this->getRequest()->getParam('id');
        $post    = Mage::getModel('mageplaza_betterblog/post')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($postId) {
            $post->load($postId);
        }
        Mage::register('current_post', $post);
        return $post;
    }

    /**
     * default action for post controller
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function indexAction()
    {
        $this->_title($this->__('Better Blog'))
             ->_title($this->__('Manage Posts'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * new post action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * edit post action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function editAction()
    {
        $postId  = (int) $this->getRequest()->getParam('id');
        $post    = $this->_initPost();
        if ($postId && !$post->getId()) {
            $this->_getSession()->addError(
                Mage::helper('mageplaza_betterblog')->__('This post no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getPostData(true)) {
            $post->setData($data);
        }
        $this->_title($post->getPostTitle());
        Mage::dispatchEvent(
            'mageplaza_betterblog_post_edit_action',
            array('post' => $post)
        );
        $this->loadLayout();
        if ($post->getId()) {
            if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
                $switchBlock->setDefaultStoreName(Mage::helper('mageplaza_betterblog')->__('Default Values'))
                    ->setWebsiteIds($post->getWebsiteIds())
                    ->setSwitchUrl(
                        $this->getUrl(
                            '*/*/*',
                            array(
                                '_current'=>true,
                                'active_tab'=>null,
                                'tab' => null,
                                'store'=>null
                            )
                        )
                    );
            }
        } else {
            $this->getLayout()->getBlock('left')->unsetChild('store_switcher');
        }
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * save post action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $postId   = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $post     = $this->_initPost();
            $postData = $this->getRequest()->getPost('post', array());
            $post->addData($postData);
            $post->setAttributeSetId($post->getDefaultAttributeSetId());
            if (isset($data['tags'])) {
                $tags = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['tags']);
                $post->setTagsData($tags);
            }
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $post->setCategoriesData($categories);
                }
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $post->setData($attributeCode, false);
                }
            }
            try {
                $post->save();
                $postId = $post->getId();
                $this->_getSession()->addSuccess(
                    Mage::helper('mageplaza_betterblog')->__('Post was saved')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setPostData($postData);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(
                    Mage::helper('mageplaza_betterblog')->__('Error saving post')
                )
                ->setPostData($postData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect(
                '*/*/edit',
                array(
                    'id'    => $postId,
                    '_current'=>true
                )
            );
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    /**
     * delete post
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $post = Mage::getModel('mageplaza_betterblog/post')->load($id);
            try {
                $post->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('mageplaza_betterblog')->__('The posts has been deleted.')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect(
            $this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store')))
        );
    }

    /**
     * mass delete posts
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function massDeleteAction()
    {
        $postIds = $this->getRequest()->getParam('post');
        if (!is_array($postIds)) {
            $this->_getSession()->addError($this->__('Please select posts.'));
        } else {
            try {
                foreach ($postIds as $postId) {
                    $post = Mage::getSingleton('mageplaza_betterblog/post')->load($postId);
                    Mage::dispatchEvent(
                        'mageplaza_betterblog_controller_post_delete',
                        array('post' => $post)
                    );
                    $post->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('mageplaza_betterblog')->__('Total of %d record(s) have been deleted.', count($postIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function massStatusAction()
    {
        $postIds = $this->getRequest()->getParam('post');
        if (!is_array($postIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('mageplaza_betterblog')->__('Please select posts.')
            );
        } else {
            try {
                foreach ($postIds as $postId) {
                $post = Mage::getSingleton('mageplaza_betterblog/post')->load($postId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d posts were successfully updated.', count($postIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('mageplaza_betterblog')->__('There was an error updating posts.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * restrict access
     *
     * @access protected
     * @return bool
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     * @author Sam
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('mageplaza_betterblog/post');
    }

    /**
     * Export posts in CSV format
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function exportCsvAction()
    {
        $fileName   = 'posts.csv';
        $content    = $this->getLayout()->createBlock('mageplaza_betterblog/adminhtml_post_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export posts in Excel format
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function exportExcelAction()
    {
        $fileName   = 'post.xls';
        $content    = $this->getLayout()->createBlock('mageplaza_betterblog/adminhtml_post_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export posts in XML format
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function exportXmlAction()
    {
        $fileName   = 'post.xml';
        $content    = $this->getLayout()->createBlock('mageplaza_betterblog/adminhtml_post_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * wysiwyg editor action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function wysiwygAction()
    {
        $elementId     = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId       = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'mageplaza_betterblog/adminhtml_betterblog_helper_form_wysiwyg_content',
            '',
            array(
                'editor_element_id' => $elementId,
                'store_id'          => $storeId,
                'store_media_url'   => $storeMediaUrl,
            )
        );
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     *  on the current post
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function tagsAction()
    {
        $this->_initPost();
        $this->loadLayout();
        $this->getLayout()->getBlock('mageplaza_betterblog.post.edit.tab.tag')
            ->setPostTags($this->getRequest()->getPost('tags', null));
        $this->renderLayout();
    }

    /**
     *  on the current post
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function tagsGridAction()
    {
        $this->_initPost();
        $this->loadLayout();
        $this->getLayout()->getBlock('mageplaza_betterblog.post.edit.tab.tag')
            ->setPostTags($this->getRequest()->getPost('tags', null));
        $this->renderLayout();
    }
    /**
     * get  action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function categoriesAction()
    {
        $this->_initPost();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child   action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function categoriesJsonAction()
    {
        $this->_initPost();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mageplaza_betterblog/adminhtml_post_edit_tab_category')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}
