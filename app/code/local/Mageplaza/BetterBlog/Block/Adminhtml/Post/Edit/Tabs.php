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
 * Post admin edit tabs
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('post_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mageplaza_betterblog')->__('Post Information'));
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tabs
     * @author Sam
     */
    protected function _prepareLayout()
    {
        $post = $this->getPost();
        $entity = Mage::getModel('eav/entity_type')
            ->load('mageplaza_betterblog_post', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId());
        $attributes->addFieldToFilter(
            'attribute_code',
            array(
                'nin' => array('meta_title', 'meta_description', 'meta_keywords')
            )
        );
        $attributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'info',
            array(
                'label'   => Mage::helper('mageplaza_betterblog')->__('Post Information'),
                'content' => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_post_edit_tab_attributes'
                )
                ->setAttributes($attributes)
                ->toHtml(),
            )
        );
        $seoAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId())
            ->addFieldToFilter(
                'attribute_code',
                array(
                    'in' => array('meta_title', 'meta_description', 'meta_keywords')
                )
            );
        $seoAttributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'meta',
            array(
                'label'   => Mage::helper('mageplaza_betterblog')->__('Meta'),
                'title'   => Mage::helper('mageplaza_betterblog')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'mageplaza_betterblog/adminhtml_post_edit_tab_attributes'
                )
                ->setAttributes($seoAttributes)
                ->toHtml(),
            )
        );
        $this->addTab(
            'categories',
            array(
                'label' => Mage::helper('mageplaza_betterblog')->__('Categories'),
                'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        $this->addTab(
            'tags',
            array(
                'label' => Mage::helper('mageplaza_betterblog')->__('Tags'),
                'url'   => $this->getUrl('*/*/tags', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve post entity
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Post
     * @author Sam
     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }
}
