<?php

class Mageplaza_BetterBlog_Model_Adminhtml_Observer{

    public function scheduledGenerateSitemaps()
    {
        Mage::getModel('mageplaza_betterblog/sitemap')->generateXml();
    }
}