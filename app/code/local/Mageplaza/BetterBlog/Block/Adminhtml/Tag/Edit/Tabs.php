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
 * Tag admin edit tabs
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Sam
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mageplaza_betterblog')->__('Tag'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tabs
     * @author Sam
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_tag',
            array(
                'label'   => Mage::helper('mageplaza_betterblog')->__('Tag'),
                'title'   => Mage::helper('mageplaza_betterblog')->__('Tag'),
                'content' => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_tag_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'form_meta_tag',
            array(
                'label'   => Mage::helper('mageplaza_betterblog')->__('Meta'),
                'title'   => Mage::helper('mageplaza_betterblog')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_tag_edit_tab_meta'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_tag',
                array(
                    'label'   => Mage::helper('mageplaza_betterblog')->__('Store views'),
                    'title'   => Mage::helper('mageplaza_betterblog')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'mageplaza_betterblog/adminhtml_tag_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        $this->addTab(
            'posts',
            array(
                'label' => Mage::helper('mageplaza_betterblog')->__('Posts'),
                'url'   => $this->getUrl('*/*/posts', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve tag entity
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Tag
     * @author Sam
     */
    public function getTag()
    {
        return Mage::registry('current_tag');
    }
}
