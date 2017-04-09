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
 * Tag widget block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Tag_Widget_View extends Mage_Core_Block_Template implements
    Mage_Widget_Block_Interface
{
    protected $_htmlTemplate = 'mageplaza_betterblog/tag/widget/view.phtml';

    /**
     * Prepare a for widget
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Tag_Widget_View
     * @author Sam
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $tagId = $this->getData('tag_id');
        if ($tagId) {
            $tag = Mage::getModel('mageplaza_betterblog/tag')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($tagId);
            if ($tag->getStatus()) {
                $this->setCurrentTag($tag);
                $this->setTemplate($this->_htmlTemplate);
            }
        }
        return $this;
    }
}
