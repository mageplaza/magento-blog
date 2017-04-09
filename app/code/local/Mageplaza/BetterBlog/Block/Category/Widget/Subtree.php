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
 * Category subtree block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Category_Widget_Subtree extends Mageplaza_BetterBlog_Block_Category_List implements
    Mage_Widget_Block_Interface
{
    protected $_template = 'mageplaza_betterblog/category/widget/subtree.phtml';
    /**
     * prepare the layout
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Category_Widget_Subtree
     * @author Sam
     */
    protected function _prepareLayout()
    {
        $this->getCategories()->addFieldToFilter('entity_id', $this->getCategoryId());
        return $this;
    }

    /**
     * get the display mode
     *
     * @access protected
     * @return int
     * @author Sam
     */
    protected function _getDisplayMode()
    {
        return 1;
    }

    /**
     * get the element id
     *
     * @access protected
     * @return int
     * @author Sam
     */
    public function getUniqueId()
    {
        if (!$this->getData('uniq_id')) {
            $this->setData('uniq_id', uniqid('subtree'));
        }
        return $this->getData('uniq_id');
    }
}
