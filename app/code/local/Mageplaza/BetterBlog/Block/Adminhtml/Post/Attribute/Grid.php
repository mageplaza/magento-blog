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
 * Post attributes grid
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare post attributes grid collection object
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Attribute_Grid
     * @author Sam
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('mageplaza_betterblog/post_attribute_collection')
            ->addVisibleFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare post attributes grid columns
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Attribute_Grid
     * @author Sam
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumnAfter(
            'is_global',
            array(
                'header'   => Mage::helper('mageplaza_betterblog')->__('Scope'),
                'sortable' => true,
                'index'    => 'is_global',
                'type'     => 'options',
                'options'  => array(
                    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE   =>
                        Mage::helper('mageplaza_betterblog')->__('Store View'),
                    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>
                        Mage::helper('mageplaza_betterblog')->__('Website'),
                    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL  =>
                        Mage::helper('mageplaza_betterblog')->__('Global'),
                ),
                'align' => 'center',
            ),
            'is_user_defined'
        );
        return $this;
    }
}
