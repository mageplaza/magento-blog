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
 * Category admin controller
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Adminhtml_Betterblog_CategoryController extends Mageplaza_BetterBlog_Controller_Adminhtml_BetterBlog
{
    /**
     * init category
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Category
     * @author Sam
     */
    protected function _initCategory()
    {
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        $category = Mage::getModel('mageplaza_betterblog/category');
        if ($categoryId) {
            $category->load($categoryId);
        } else {
            $category->setData($category->getDefaultValues());
        }
        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setCategoryActiveTabId($activeTabId);
        }
        Mage::register('category', $category);
        Mage::register('current_category', $category);
        return $category;
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
        $this->_forward('edit');
    }

    /**
     * Add new category form
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsCategoryActiveTabId();
        $this->_forward('edit');
    }

    /**
     * Edit category page
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function editAction()
    {
        $params['_current'] = true;
        $redirect = false;
        $parentId = (int) $this->getRequest()->getParam('parent');
        $categoryId = (int) $this->getRequest()->getParam('id');
        $_prevCategoryId = Mage::getSingleton('admin/session')->getLastEditedCategory(true);
        if ($_prevCategoryId &&
            !$this->getRequest()->getQuery('isAjax') &&
            !$this->getRequest()->getParam('clear')) {
            $this->getRequest()->setParam('id', $_prevCategoryId);
        }
        if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }
        if (!($category = $this->_initCategory())) {
            return;
        }
        $this->_title($categoryId ? $category->getName() : $this->__('New Category'));
        $data = Mage::getSingleton('adminhtml/session')->getCategoryData(true);
        if (isset($data['category'])) {
            $category->addData($data['category']);
        }
        if ($this->getRequest()->getQuery('isAjax')) {
            $breadcrumbsPath = $category->getPath();
            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getCategoryDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    } else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }
            Mage::getSingleton('admin/session')->setLastEditedCategory($category->getId());
            $this->loadLayout();
            $eventResponse = new Varien_Object(
                array(
                    'content' => $this->getLayout()->getBlock('category.edit')->getFormHtml().
                        $this->getLayout()->getBlock('category.tree')->getBreadcrumbsJavascript(
                            $breadcrumbsPath,
                            'editingCategoryBreadcrumbs'
                        ),
                    'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                )
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($eventResponse->getData()));
            return;
        }
        $this->loadLayout();
        $this->_title(Mage::helper('mageplaza_betterblog')->__('Better Blog'))
             ->_title(Mage::helper('mageplaza_betterblog')->__('Categories'));
        $this->_setActiveMenu('mageplaza_betterblog/category');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('category');

        $this->_addBreadcrumb(
            Mage::helper('mageplaza_betterblog')->__('Manage Categories'),
            Mage::helper('mageplaza_betterblog')->__('Manage Categories')
        );
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * Get tree node (Ajax version)
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function categoriesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setCategoryIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setCategoryIsTreeWasExpanded(false);
        }
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $categoryId);
            if (!$category = $this->_initCategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('mageplaza_betterblog/adminhtml_category_tree')
                    ->getTreeJson($category)
            );
        }
    }

    /**
     * Move category action
     * @access public
     * @author Sam
     */
    public function moveAction()
    {
        $category = $this->_initCategory();
        if (!$category) {
            $this->getResponse()->setBody(
                Mage::helper('mageplaza_betterblog')->__('Category move error')
            );
            return;
        }
        $parentNodeId   = $this->getRequest()->getPost('pid', false);
        $prevNodeId = $this->getRequest()->getPost('aid', false);
        try {
            $category->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody(
                Mage::helper('mageplaza_betterblog')->__('Category move error')
            );
            Mage::logException($e);
        }
    }

    /**
     * Tree Action
     * Retrieve category tree
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function treeAction()
    {
        $categoryId = (int) $this->getRequest()->getParam('id');
        $category = $this->_initCategory();
        $block = $this->getLayout()->createBlock('mageplaza_betterblog/adminhtml_category_tree');
        $root  = $block->getRoot();
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                array(
                    'data' => $block->getTree(),
                    'parameters' => array(
                        'text'          => $block->buildNodeName($root),
                        'draggable'     => false,
                        'allowDrop'     => ($root->getIsVisible()) ? true : false,
                        'id'            => (int) $root->getId(),
                        'expanded'      => (int) $block->getIsWasExpanded(),
                        'category_id' => (int) $category->getId(),
                        'root_visible'  => (int) $root->getIsVisible()
                    )
                )
            )
        );
    }

    /**
     * Build response for refresh input element 'path' in form
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function refreshPathAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            $category = Mage::getModel('mageplaza_betterblog/category')->load($id);
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    array(
                       'id' => $id,
                       'path' => $category->getPath(),
                    )
                )
            );
        }
    }

    /**
     * Delete category action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function deleteAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            try {
                $category = Mage::getModel('mageplaza_betterblog/category')->load($id);
                Mage::getSingleton('admin/session')->setCategoryDeletedPath($category->getPath());

                $category->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('mageplaza_betterblog')->__('The category has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('mageplaza_betterblog')->__('An error occurred while trying to delete the category.')
                );
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                Mage::logException($e);
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Sam
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('mageplaza_betterblog/category');
    }

    /**
     * Category save action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function saveAction()
    {
        if (!$category = $this->_initCategory()) {
            return;
        }
        $refreshTree = 'false';
        if ($data = $this->getRequest()->getPost('category')) {
            $category->addData($data);
            if (!$category->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    $parentId = Mage::helper('mageplaza_betterblog/category')->getRootCategoryId();
                }
                $parentCategory = Mage::getModel('mageplaza_betterblog/category')->load($parentId);
                $category->setPath($parentCategory->getPath());
            }
            try {
                $posts = $this->getRequest()->getPost('category_posts', -1);
                if ($posts != -1) {
                    $postData = array();
                    parse_str($posts, $postData);
                    foreach ($postData as $id => $position) {
                        $post[$id]['position'] = $position;
                    }
                    $category->setPostsData($postData);
                }
                $category->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('mageplaza_betterblog')->__('The category has been saved.')
                );
                $refreshTree = 'true';
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage())->setCategoryData($data);
                Mage::logException($e);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $category->getId()));
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, '.$refreshTree.');</script>'
        );
    }

    /**
     * get  grid action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function postsgridAction()
    {
        if (!$category = $this->_initCategory()) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'mageplaza_betterblog/adminhtml_category_edit_tab_post',
                'category.post.grid'
            )
            ->toHtml()
        );
    }
}
