<?php

class Mageplaza_BetterBlog_IndexController extends Mage_Core_Controller_Front_Action{

    /**
     * Redirect to main page
     */
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
        if (Mage::helper('mageplaza_betterblog/post')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('mageplaza_betterblog')->__('Home'),
                        'link' => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'posts',
                    array(
                        'label' => Mage::helper('mageplaza_betterblog')->__('Blog'),
                        'link' => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('mageplaza_betterblog/post')->getPostsUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('mageplaza_betterblog/post/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('mageplaza_betterblog/post/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('mageplaza_betterblog/post/meta_description'));
        }
        $this->renderLayout();
    }

    public function testAction()
    {
//        Mage::getModel('mageplaza_betterblog/import')->aw();
    }

}