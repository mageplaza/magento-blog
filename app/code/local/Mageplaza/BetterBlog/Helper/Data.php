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
 * BetterBlog default helper
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Helper_Data extends Mage_Core_Helper_Abstract
{

    const URL_REWRITE_ID_PATH = 'mp_better_blog';
    const URL_WELCOME_ID_KEY = 'welcome-to-our-blog';
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Sam
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    /**
     * check can show comment count
     * @return bool
     */
    public function canShowCommentCount()
    {
        $config = Mage::helper('mageplaza_betterblog/config');
        return $config->getCommentConfig('type') == 'default';
    }

    /**
     * format comment count
     * @return string
     */
    public function formatCommentCount($count = 0)
    {
        if(!$count) $count = 0;
        return ($count > 1) ? $count .' ' .  Mage::helper('mageplaza_betterblog')->__('comments') :
            $count .' ' .  Mage::helper('mageplaza_betterblog')->__('comment');
    }

    public function canShowCommentWidget()
    {
        $config = Mage::helper('mageplaza_betterblog/config');
        return ($config->getCommentConfig('type') == 'default' &&
            $config->getSidebarConfig('enable_comment_widget') ) ;
    }


}
