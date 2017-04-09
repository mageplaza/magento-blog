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
 * BetterBlog RSS block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Rss extends Mage_Core_Block_Template
{
    /**
     * RSS feeds for this block
     */
    protected $_feeds = array();

    /**
     * add a new feed
     *
     * @access public
     * @param string $label
     * @param string $url
     * @param bool $prepare
     * @return Mageplaza_BetterBlog_Block_Rss
     * @author Sam
     */
    public function addFeed($label, $url, $prepare = false)
    {
        $link = ($prepare ? $this->getUrl($url) : $url);
        $feed = new Varien_Object();
        $feed->setLabel($label);
        $feed->setUrl($link);
        $this->_feeds[$link] = $feed;
        return $this;
    }

    /**
     * get the current feeds
     *
     * @access public
     * @return array()
     * @author Sam
     */
    public function getFeeds()
    {
        return $this->_feeds;
    }
}
