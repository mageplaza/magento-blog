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
 * Post admin attribute block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @author Sam
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_post_attribute';
        $this->_blockGroup = 'mageplaza_betterblog';
        $this->_headerText = Mage::helper('mageplaza_betterblog')->__('Manage Post Attributes');
        parent::__construct();
        $this->_updateButton(
            'add',
            'label',
            Mage::helper('mageplaza_betterblog')->__('Add New Post Attribute')
        );
    }
}
