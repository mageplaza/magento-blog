<?php
class Mageplaza_BetterBlog_Helper_Config extends Mage_Core_Helper_Abstract
{

    const DEFAULT_URL = 'blog';
    /**
     * get general config by code
     *
     * @param      $code
     * @param null $store
     * @return mixed
     */
    public function getGeneralConfig($code, $store = null)
    {
        return Mage::getStoreConfig('mageplaza_betterblog/general/' . $code, $store);
    }


    /**
     * get blog url
     *
     * @param      $code
     * @param null $store
     * @return mixed
     */
    public function getBlogUrl($store = null)
    {
        return Mage::getUrl($this->getBlogRoute(), array('_store' => $store));
    }

    public function getBlogRoute()
    {
        return self::DEFAULT_URL;
    }



    /**
     * get post config by code
     *
     * @param      $code
     * @param null $store
     * @return mixed
     */

    public function getPostConfig($code, $store = null)
    {
        return Mage::getStoreConfig('mageplaza_betterblog/post/' . $code, $store);
    }

    /**
     * get comment config by code
     *
     * @param      $code
     * @param null $store
     * @return mixed
     */

    public function getCommentConfig($code, $store = null)
    {
        return Mage::getStoreConfig('mageplaza_betterblog/comment/' . $code, $store);
    }

    /**
     * get category config by code
     *
     * @param      $code
     * @param null $store
     * @return mixed
     */

    public function getCategoryConfig($code, $store = null)
    {
        return Mage::getStoreConfig('mageplaza_betterblog/category/' . $code, $store);
    }

    /**
     * get tags config by code
     *
     * @param      $code
     * @param null $store
     * @return mixed
     */

    public function getTagConfig($code, $store = null)
    {
        return Mage::getStoreConfig('mageplaza_betterblog/tag/' . $code, $store);
    }

    /**
     * get sidebar config
     * @param $code
     * @param null $store
     * @return mixed
     */
    public function getSidebarConfig($code, $store = null)
    {
        return Mage::getStoreConfig('mageplaza_betterblog/sidebar/' . $code, $store);
    }













}