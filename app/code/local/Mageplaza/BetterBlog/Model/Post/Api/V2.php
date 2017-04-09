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
class Mageplaza_BetterBlog_Model_Post_Api_V2 extends Mageplaza_BetterBlog_Model_Post_Api
{
    /**
     * Post info
     *
     * @access public
     * @param int $postId
     * @return object
     * @author Sam
     */
    public function info($postId)
    {
        $result = parent::info($postId);
        $result = Mage::helper('api')->wsiArrayPacker($result);
        foreach ($result->categories as $key => $value) {
            $result->categories[$key] = array('key' => $key, 'value' => $value);
        }
        foreach ($result->tags as $key => $value) {
            $result->tags[$key] = array('key' => $key, 'value' => $value);
        }
        return $result;
    }
}
