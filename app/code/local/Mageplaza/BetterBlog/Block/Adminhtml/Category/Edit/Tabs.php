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
 * Category admin edit tabs
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Sam
     */
    public function __construct()
    {
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('mageplaza_betterblog')->__('Category'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * Prepare Layout Content
     *
     * @access public
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Category_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'form_category',
            array(
                'label'   => Mage::helper('mageplaza_betterblog')->__('Category'),
                'title'   => Mage::helper('mageplaza_betterblog')->__('Category'),
                'content' => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_category_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'form_meta_category',
            array(
                'label'   => Mage::helper('mageplaza_betterblog')->__('Meta'),
                'title'   => Mage::helper('mageplaza_betterblog')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_category_edit_tab_meta'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_category',
                array(
                    'label'   => Mage::helper('mageplaza_betterblog')->__('Store views'),
                    'title'   => Mage::helper('mageplaza_betterblog')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'mageplaza_betterblog/adminhtml_category_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        $this->addTab(
            'posts',
            array(
                'label'   => Mage::helper('mageplaza_betterblog')->__('Posts'),
                'content' => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_category_edit_tab_post',
                    'category.post.grid'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve category entity
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Category
     * @author Sam
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }
}
