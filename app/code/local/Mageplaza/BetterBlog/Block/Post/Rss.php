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
 * Post RSS block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Post_Rss extends Mage_Rss_Block_Abstract
{
    /**
     * Cache tag constant for feed reviews
     *
     * @var string
     */
    const CACHE_TAG = 'block_html_betterblog_post_rss';

    /**
     * constructor
     *
     * @access protected
     * @return void
     * @author Sam
     */
    protected function _construct()
    {
        $this->setCacheTags(array(self::CACHE_TAG));
        /*
         * setting cache to save the rss for 10 minutes
         */
        $this->setCacheKey('mageplaza_betterblog_post_rss');
        $this->setCacheLifetime(600);
    }

    /**
     * toHtml method
     *
     * @access protected
     * @return string
     * @author Sam
     */
    protected function _toHtml()
    {
        $url    = Mage::helper('mageplaza_betterblog/post')->getPostsUrl();
        $title  = Mage::helper('mageplaza_betterblog')->__('Posts');
        $rssObj = Mage::getModel('rss/rss');
        $data  = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $url,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);
        $collection = Mage::getModel('mageplaza_betterblog/post')->getCollection()
            ->addFieldToFilter('status', 1)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('in_rss', 1)
            ->setOrder('created_at');
        $collection->load();
        foreach ($collection as $item) {
            $description = '<p>';
            $description .= '<div>'.
                Mage::helper('mageplaza_betterblog')->__('Name').': 
                '.$item->getPostTitle().
                '</div>';
            $description .= '<div>'.
                Mage::helper('mageplaza_betterblog')->__('Short Description').': 
                '.$item->getPostExcerpt().
                '</div>';
            $description .= '</p>';
            $data = array(
                'title'       => $item->getPostTitle(),
                'link'        => $item->getPostUrl(),
                'description' => $description
            );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }
}
