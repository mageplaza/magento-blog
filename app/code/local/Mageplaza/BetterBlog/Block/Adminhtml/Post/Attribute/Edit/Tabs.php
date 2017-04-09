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
 * Adminhtml post attribute edit page tabs
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * constructor
     *
     * @access public
     * @author Sam
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('post_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mageplaza_betterblog')->__('Attribute Information'));
    }

    /**
     * add attribute tabs
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Adminhtml_Post_Attribute_Edit_Tabs
     * @author Sam
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main',
            array(
                'label'     => Mage::helper('mageplaza_betterblog')->__('Properties'),
                'title'     => Mage::helper('mageplaza_betterblog')->__('Properties'),
                'content'   => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_post_attribute_edit_tab_main'
                )
                ->toHtml(),
                'active'    => true
            )
        );
        $this->addTab(
            'labels',
            array(
                'label'     => Mage::helper('mageplaza_betterblog')->__('Manage Label / Options'),
                'title'     => Mage::helper('mageplaza_betterblog')->__('Manage Label / Options'),
                'content'   => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_post_attribute_edit_tab_options'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }
}
