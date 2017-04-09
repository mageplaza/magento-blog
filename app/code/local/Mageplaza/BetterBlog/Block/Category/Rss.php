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
 * Category RSS block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Category_Rss extends Mage_Rss_Block_Abstract
{
    /**
     * Cache tag constant for feed reviews
     *
     * @var string
     */
    const CACHE_TAG = 'block_html_betterblog_category_rss';

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
        $this->setCacheKey('mageplaza_betterblog_category_rss');
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
        $url    = Mage::helper('mageplaza_betterblog/category')->getCategoriesUrl();
        $title  = Mage::helper('mageplaza_betterblog')->__('Categories');
        $rssObj = Mage::getModel('rss/rss');
        $data  = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $url,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);
        $collection = Mage::getModel('mageplaza_betterblog/category')->getCollection()
            ->addFieldToFilter('status', 1)
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('in_rss', 1)
            ->setOrder('created_at');
        $collection->load();
        foreach ($collection as $item) {
            $description = '<p>';
            if (!$item->getStatusPath()) {
                continue;
            }            $description .= '<div>'.
                Mage::helper('mageplaza_betterblog')->__('Name').': 
                '.$item->getName().
                '</div>';
            $description .= '</p>';
            $data = array(
                'title'       => $item->getName(),
                'link'        => $item->getCategoryUrl(),
                'description' => $description
            );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }
}
