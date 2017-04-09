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
class Mageplaza_BetterBlog_Model_Tag_Api_V2 extends Mageplaza_BetterBlog_Model_Tag_Api
{
    /**
     * Tag info
     *
     * @access public
     * @param int $tagId
     * @return object
     * @author Sam
     */
    public function info($tagId)
    {
        $result = parent::info($tagId);
        $result = Mage::helper('api')->wsiArrayPacker($result);
        foreach ($result->posts as $key => $value) {
            $result->posts[$key] = array('key' => $key, 'value' => $value);
        }
        return $result;
    }
}
