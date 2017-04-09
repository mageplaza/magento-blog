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
 * Post attribute API V2 model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Post_Attribute_Api_V2 extends Mageplaza_BetterBlog_Model_Post_Attribute_Api
{
    /**
     * Create new post attribute
     *
     * @access public
     * @param array $data input data
     * @return integer
     * @author Sam
     */
    public function create($data)
    {
        $helper = Mage::helper('api');
        $helper->v2AssociativeArrayUnpacker($data);
        Mage::helper('api')->toArray($data);
        return parent::create($data);
    }

    /**
     * Update product attribute
     *
     * @access public
     * @param string|integer $attribute attribute code or ID
     * @param array $data
     * @return boolean
     * @author Sam
     */
    public function update($attribute, $data)
    {
        $helper = Mage::helper('api');
        $helper->v2AssociativeArrayUnpacker($data);
        Mage::helper('api')->toArray($data);
        return parent::update($attribute, $data);
    }

    /**
     * Add option to select or multiselect attribute
     *
     * @access public
     * @param  integer|string $attribute attribute ID or code
     * @param  array $data
     * @return bool
     * @author Sam
     */
    public function addOption($attribute, $data)
    {
        Mage::helper('api')->toArray($data);
        return parent::addOption($attribute, $data);
    }

    /**
     * Get full information about attribute with list of options
     *
     * @access public
     * @param integer|string $attribute attribute ID or code
     * @return array
     * @author Sam
     */
    public function info($attribute)
    {
        $result = parent::info($attribute);
        if (!empty($result['additional_fields'])) {
            $keys = array_keys($result['additional_fields']);
            foreach ($keys as $key) {
                $result['additional_fields'][] = array(
                    'key'   => $key,
                    'value' => $result['additional_fields'][$key]
                );
                unset($result['additional_fields'][$key]);
            }
        }
        return $result;
    }
}
