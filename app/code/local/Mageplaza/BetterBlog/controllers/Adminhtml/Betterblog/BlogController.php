<?php

class Mageplaza_BetterBlog_Adminhtml_Betterblog_BlogController extends Mage_Adminhtml_Controller_Action
{
    public function generateSitemapAction()
    {
        Mage::getModel('mageplaza_betterblog/generateSitemap')->generateXml();
    }

    public function importAwAction()
    {


    }
}