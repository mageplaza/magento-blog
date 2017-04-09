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
 * Tag helper
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Helper_Tag extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the tags list page
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getTagsUrl()
    {
        if ($listKey = Mage::getStoreConfig('mageplaza_betterblog/tag/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('mageplaza_betterblog/tag/index');
    }

    /**
     * check if breadcrumbs can be used
     *
     * @access public
     * @return bool
     * @author Sam
     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('mageplaza_betterblog/tag/breadcrumbs');
    }

    /**
     * check if the rss for tag is enabled
     *
     * @access public
     * @return bool
     * @author Sam
     */
    public function isRssEnabled()
    {
        return  Mage::getStoreConfigFlag('rss/config/active') &&
            Mage::getStoreConfigFlag('mageplaza_betterblog/tag/rss');
    }

    /**
     * get the link to the tag rss list
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getRssUrl()
    {
        return Mage::getUrl('mageplaza_betterblog/tag/rss');
    }
}
