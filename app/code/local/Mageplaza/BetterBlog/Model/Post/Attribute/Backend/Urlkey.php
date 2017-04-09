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
 * Admin backend source model for url key
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Post_Attribute_Backend_Urlkey extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Before save
     *
     * @access public
     * @param Varien_Object $object
     * @return Mageplaza_BetterBlog_Model_Post_Attribute_Backend_Urlkey
     * @author Sam
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $urlKey = $object->getData($attributeName);
        if ($urlKey == '') {
            $urlKey = $object->getPostTitle();
        }
        $urlKey = $this->formatUrlKey($urlKey);
        $validKey = false;
        while (!$validKey) {
            $entityId = Mage::getResourceModel('mageplaza_betterblog/post')
                ->checkUrlKey($urlKey, $object->getStoreId(), false);
            if ($entityId == $object->getId() || empty($entityId)) {
                $validKey = true;
            } else {
                $parts = explode('-', $urlKey);
                $last = $parts[count($parts) - 1];
                if (!is_numeric($last)) {
                    $urlKey = $urlKey.'-1';
                } else {
                    $suffix = '-'.($last + 1);
                    unset($parts[count($parts) - 1]);
                    $urlKey = implode('-', $parts).$suffix;
                }
            }
        }
        $object->setData($attributeName, $urlKey);
        return $this;
    }

    /**
     * format url key
     *
     * @access public
     * @param string $str
     * @return string
     * @author Sam
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }
}
