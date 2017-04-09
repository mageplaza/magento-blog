<?php
class Mageplaza_BetterBlog_Model_Feed extends Mage_AdminNotification_Model_Feed{

    const FEED_URL = 'http://feeds.feedburner.com/mageplaza';
    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        $this->_feedUrl = self::FEED_URL;
        return $this->_feedUrl;
    }


    public function updateFeed(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn() && !Mage::registry('mp_is_updated_feed')) {
            $this->checkUpdate();
            Mage::register('mp_is_updated_feed',true);
        }
    }


}