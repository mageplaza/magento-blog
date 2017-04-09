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
 * Post REST API model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Api2_Post extends Mage_Api2_Model_Resource
{

    /**
     * Get available attributes of API resource
     *
     * @access public
     * @param string $userType
     * @param string $operation
     * @return array
     * @author Sam
     */
    public function getAvailableAttributes($userType, $operation)
    {
        $attributes = $this->getAvailableAttributesFromConfig();
        $entityType = Mage::getModel('eav/entity_type')->loadByCode('mageplaza_betterblog_post');
        $entityOnlyAttrs = $this->getEntityOnlyAttributes($userType, $operation);
        foreach ($entityType->getAttributeCollection() as $attribute) {
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }
        $excludedAttrs = $this->getExcludedAttributes($userType, $operation);
        $includedAttrs = $this->getIncludedAttributes($userType, $operation);
        foreach ($attributes as $code => $label) {
            if (in_array($code, $excludedAttrs) || ($includedAttrs && !in_array($code, $includedAttrs))) {
                unset($attributes[$code]);
            }
            if (in_array($code, $entityOnlyAttrs)) {
                $attributes[$code] .= ' *';
            }
        }
        return $attributes;
    }
}
