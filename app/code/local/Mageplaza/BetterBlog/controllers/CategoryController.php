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
 * Category front contrller
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_CategoryController extends Mage_Core_Controller_Front_Action
{

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
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('mageplaza_betterblog/category')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('mageplaza_betterblog')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'categories',
                    array(
                        'label' => Mage::helper('mageplaza_betterblog')->__('Categories'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('mageplaza_betterblog/category')->getCategoriesUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('mageplaza_betterblog/category/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('mageplaza_betterblog/category/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('mageplaza_betterblog/category/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Category
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Model_Category
     * @author Sam
     */
    protected function _initCategory()
    {
        $categoryId   = $this->getRequest()->getParam('id', 0);
        $category     = Mage::getModel('mageplaza_betterblog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);
        if (!$category->getId()) {
            return false;
        } elseif (!$category->getStatus()) {
            return false;
        }
        return $category;
    }

    /**
     * view category action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function viewAction()
    {
        $category = $this->_initCategory();
        if (!$category) {
            $this->_forward('no-route');
            return;
        }
        if (!$category->getStatusPath()) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_category', $category);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('betterblog-category betterblog-category' . $category->getId());
        }
        if (Mage::helper('mageplaza_betterblog/category')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('mageplaza_betterblog')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'posts',
                    array(
                        'label' => Mage::helper('mageplaza_betterblog')->__('Blog'),
                        'link'  => Mage::helper('mageplaza_betterblog/post')->getPostsUrl(),
                    )
                );
                $parents = $category->getParentCategories();
                foreach ($parents as $parent) {
                    if ($parent->getId() != Mage::helper('mageplaza_betterblog/category')->getRootCategoryId() &&
                        $parent->getId() != $category->getId()) {
                        $breadcrumbBlock->addCrumb(
                            'category-'.$parent->getId(),
                            array(
                                'label'    => $parent->getName(),
                                'link'    => $link = $parent->getCategoryUrl(),
                            )
                        );
                    }
                }
                $breadcrumbBlock->addCrumb(
                    'category',
                    array(
                        'label' => $category->getName(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $category->getCategoryUrl());
        }
        if ($headBlock) {
            if ($category->getMetaTitle()) {
                $headBlock->setTitle($category->getMetaTitle());
            } else {
                $headBlock->setTitle($category->getName());
            }
            $headBlock->setKeywords($category->getMetaKeywords());
            $headBlock->setDescription($category->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * categories rss list action
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function rssAction()
    {
        if (Mage::helper('mageplaza_betterblog/category')->isRssEnabled()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('nofeed', 'index', 'rss');
        }
    }
}
