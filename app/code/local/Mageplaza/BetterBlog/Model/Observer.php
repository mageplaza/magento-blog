<?php

class Mageplaza_BetterBlog_Model_Observer
{
    public function generateSitemap($observer)
    {
        $object = $observer->getObject();
        $io = $observer->getIo();
        $storeId = $object->getStoreId();
        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');

        /**
         * Generate blog categories sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/category/changefreq', $storeId);
        $priority = (string)Mage::getStoreConfig('sitemap/category/priority', $storeId);
        $collection = Mage::getResourceModel('mageplaza_betterblog/category_collection')
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('status', 1);

        $categories = new Varien_Object();
        $categories->setItems($collection);

        foreach ($categories->getItems() as $item) {
            $xml = sprintf(
                '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($object->filterUrl($item->getCategoryUrl())),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
        }
        unset($collection);

        /**
         * Generate blog post sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/product/changefreq', $storeId);
        $priority = (string)Mage::getStoreConfig('sitemap/product/priority', $storeId);
        $collection = Mage::getResourceModel('mageplaza_betterblog/post_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)
            ->setOrder('created_at', 'desc');
        $posts = new Varien_Object();
        $posts->setItems($collection);

        foreach ($posts->getItems() as $item) {
            $xml = sprintf(
                '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($object->filterUrl($item->getPostUrl())),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
        }

    }
}