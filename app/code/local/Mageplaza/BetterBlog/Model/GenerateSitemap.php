<?php

class Mageplaza_BetterBlog_Model_GenerateSitemap extends Mage_Core_Model_Abstract
{

    /**
     * Real file path
     *
     * @var string
     */
    protected $_filePath;

    protected $_sitemapName = 'blog.xml';

    protected function _construct()
    {
        $io = new Varien_Io_File();
        $realPath = $io->getCleanPath(Mage::getBaseDir() . '/sitemap');

        $this->setSitemapPath(rtrim(str_replace(str_replace('\\', '/', Mage::getBaseDir()), '', $realPath), '/') . '/');

        if (!$io->isWriteable($realPath)) {
            Mage::throwException(Mage::helper('sitemap')->__('Please make sure that "%s" is writable by web-server.', $this->getSitemapPath()));
        }


    }

    /**
     * Return real file path
     *
     * @return string
     */
    protected function getPath()
    {
        if (is_null($this->_filePath)) {
            $this->_filePath = str_replace('//', '/', Mage::getBaseDir() .
                $this->getSitemapPath());
        }
        return $this->_filePath;
    }

    /**
     * Return full file name with path
     *
     * @return string
     */
    public function getPreparedFilename()
    {
        return $this->getPath() . $this->_sitemapName;
    }

    public function getSitemapFilename()
    {
        return $this->_sitemapName;
    }

    /**
     * Generate XML file
     *
     * @return Mage_Sitemap_Model_Sitemap
     */
    public function generateXml()
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));

        $io->streamOpen($this->getSitemapFilename());

        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

        $storeId = $this->getStoreId();
        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');


        /**
         * Generate blog categories sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/category/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/category/priority', $storeId);
        $collection = Mage::getResourceModel('mageplaza_betterblog/category_collection')
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('status', 1)
        ;

        $categories = new Varien_Object();
        $categories->setItems($collection);
        Mage::dispatchEvent('sitemap_betterblog_categories_generating_before', array(
            'collection' => $categories
        ));
        foreach ($categories->getItems() as $item) {
            $xml = sprintf(
                '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($this->filterUrl($item->getCategoryUrl())),
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

        Mage::dispatchEvent('sitemap_betterblog_generating_before', array(
            'collection' => $posts,
            'io' => $io,
        ));

        foreach ($posts->getItems() as $item) {
            $xml = sprintf(
                '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($this->filterUrl($item->getPostUrl())),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
        }


        $io->streamWrite('</urlset>');
        $io->streamClose();

        Mage::getSingleton('core/session')->addSuccess('Generated Sitemap successfully!');
    }

    public function filterUrl($url)
    {
        $url = str_replace('index.php/','',$url);
        return $url;
    }
}