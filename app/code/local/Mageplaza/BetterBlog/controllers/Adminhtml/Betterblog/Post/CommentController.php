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
class Mageplaza_BetterBlog_Adminhtml_Betterblog_Post_CommentController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init the comment
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Post_Comment
     * @author Sam
     */
    protected function _initComment()
    {
        $commentId  = (int) $this->getRequest()->getParam('id');
        $comment    = Mage::getModel('mageplaza_betterblog/post_comment');
        if ($commentId) {
            $comment->load($commentId);
        }
        Mage::register('current_comment', $comment);
        return $comment;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('mageplaza_betterblog')->__('Better Blog'))
             ->_title(Mage::helper('mageplaza_betterblog')->__('Posts'))
             ->_title(Mage::helper('mageplaza_betterblog')->__('Comments'));
        $this->renderLayout();
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
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit comment - action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function editAction()
    {
        $commentId    = $this->getRequest()->getParam('id');
        $comment      = $this->_initComment();
        if (!$comment->getId()) {
            $this->_getSession()->addError(
                Mage::helper('mageplaza_betterblog')->__('This comment no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $comment->setData($data);
        }
        Mage::register('comment_data', $comment);
        $post = Mage::getModel('mageplaza_betterblog/post')->load($comment->getPostId());
        Mage::register('current_post', $post);
        $this->loadLayout();
        $this->_title(Mage::helper('mageplaza_betterblog')->__('Better Blog'))
             ->_title(Mage::helper('mageplaza_betterblog')->__('Posts'))
             ->_title(Mage::helper('mageplaza_betterblog')->__('Comments'))
             ->_title($comment->getTitle());
        $this->renderLayout();
    }

    /**
     * save post - action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('comment')) {
            try {
                $comment = $this->_initComment();
                $comment->addData($data);
                if (!$comment->getCustomerId()) {
                    $comment->unsCustomerId();
                }
                $comment->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('mageplaza_betterblog')->__('Comment was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $comment->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('mageplaza_betterblog')->__('There was a problem saving the comment.')
                );
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('mageplaza_betterblog')->__('Unable to find comment to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete comment - action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $comment = Mage::getModel('mageplaza_betterblog/post_comment');
                $comment->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('mageplaza_betterblog')->__('Comment was successfully deleted.')
                );

                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('mageplaza_betterblog')->__('There was an error deleting the comment.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('mageplaza_betterblog')->__('Could not find comment to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete comments - action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function massDeleteAction()
    {
        $commentIds = $this->getRequest()->getParam('comment');
        if (!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('mageplaza_betterblog')->__('Please select comments to delete.')
            );
        } else {
            try {
                foreach ($commentIds as $commentId) {
                    $comment = Mage::getModel('mageplaza_betterblog/post_comment');
                    $comment->setId($commentId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('mageplaza_betterblog')->__(
                        'Total of %d comments were successfully deleted.',
                        count($commentIds)
                    )
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('mageplaza_betterblog')->__('There was an error deleting comments.')
                );
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
        $commentIds = $this->getRequest()->getParam('comment');
        if (!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('mageplaza_betterblog')->__('Please select comments.')
            );
        } else {
            try {
                foreach ($commentIds as $commentId) {
                    $comment = Mage::getSingleton('mageplaza_betterblog/post_comment')->load($commentId)
                         ->setStatus($this->getRequest()->getParam('status'))
                         ->setIsMassupdate(true)
                         ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d comments were successfully updated.', count($commentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('mageplaza_betterblog')->__('There was an error updating comments.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function exportCsvAction()
    {
        $fileName   = 'post_comments.csv';
        $content    = $this->getLayout()->createBlock(
            'mageplaza_betterblog/adminhtml_post_comment_grid'
        )
        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function exportExcelAction()
    {
        $fileName   = 'post_comments.xls';
        $content    = $this->getLayout()->createBlock(
            'mageplaza_betterblog/adminhtml_post_comment_grid'
        )
        ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function exportXmlAction()
    {
        $fileName   = 'post_comments.xml';
        $content    = $this->getLayout()->createBlock(
            'mageplaza_betterblog/adminhtml_post_comment_grid'
        )
        ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * check access
     *
     * @access protected
     * @return bool
     * @author Sam
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('mageplaza_betterblog/post_comments');
    }
}
