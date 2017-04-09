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
 * Post helper
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Helper_Post extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the posts list page
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getPostsUrl()
    {
//        default url
        return Mage::getUrl('', array('_direct'=>'blog'));

//        if ($listKey = Mage::getStoreConfig('mageplaza_betterblog/post/url_rewrite_list')) {
//            return Mage::getUrl('', array('_direct'=>$listKey));
//        }
//        return Mage::getUrl('mageplaza_betterblog/post/index');
    }

    /**
     * check if breadcrumbs can be used
     *
     * @access public
     * @return bool
     * @author Sam
     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('mageplaza_betterblog/post/breadcrumbs');
    }

    /**
     * check if the rss for post is enabled
     *
     * @access public
     * @return bool
     * @author Sam
     */
    public function isRssEnabled()
    {
        return  Mage::getStoreConfigFlag('rss/config/active') &&
            Mage::getStoreConfigFlag('mageplaza_betterblog/post/rss');
    }

    /**
     * get the link to the post rss list
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getRssUrl()
    {
        return Mage::getUrl('mageplaza_betterblog/post/rss');
    }

    /**
     * get base files dir
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getFileBaseDir()
    {
        return Mage::getBaseDir('media').DS.'post'.DS.'file';
    }

    /**
     * get base file url
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getFileBaseUrl()
    {
        return Mage::getBaseUrl('media').'post'.'/'.'file';
    }

    /**
     * get post attribute source model
     *
     * @access public
     * @param string $inputType
     * @return mixed (string|null)
     * @author Sam
     */
     public function getAttributeSourceModelByInputType($inputType)
     {
         $inputTypes = $this->getAttributeInputTypes();
         if (!empty($inputTypes[$inputType]['source_model'])) {
             return $inputTypes[$inputType]['source_model'];
         }
         return null;
     }

    /**
     * get attribute input types
     *
     * @access public
     * @param string $inputType
     * @return array()
     * @author Sam
     */
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = array(
            'multiselect' => array(
                'backend_model' => 'eav/entity_attribute_backend_array'
            ),
            'boolean'     => array(
                'source_model'  => 'eav/entity_attribute_source_boolean'
            ),
            'file'          => array(
                'backend_model' => 'mageplaza_betterblog/post_attribute_backend_file'
            ),
            'image'          => array(
                'backend_model' => 'mageplaza_betterblog/post_attribute_backend_image'
            ),
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }

    /**
     * get post attribute backend model
     *
     * @access public
     * @param string $inputType
     * @return mixed (string|null)
     * @author Sam
     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    /**
     * filter attribute content
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Post $post
     * @param string $attributeHtml
     * @param string @attributeName
     * @return string
     * @author Sam
     */
    public function postAttribute($post, $attributeHtml, $attributeName)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute(
            Mageplaza_BetterBlog_Model_Post::ENTITY,
            $attributeName
        );
        if ($attribute && $attribute->getId() && !$attribute->getIsWysiwygEnabled()) {
            if ($attribute->getFrontendInput() == 'textarea') {
                $attributeHtml = nl2br($attributeHtml);
            }
        }
        if ($attribute->getIsWysiwygEnabled()) {
            $attributeHtml = $this->_getTemplateProcessor()->filter($attributeHtml);
        }
        return $attributeHtml;
    }

    /**
     * get the template processor
     *
     * @access protected
     * @return Mage_Catalog_Model_Template_Filter
     * @author Sam
     */
    protected function _getTemplateProcessor()
    {
        if (null === $this->_templateProcessor) {
            $this->_templateProcessor = Mage::helper('catalog')->getPageTemplateProcessor();
        }
        return $this->_templateProcessor;
    }

    public function getWelcomeUrl()
    {
        return Mage::getUrl('', array('_direct'=>Mageplaza_BetterBlog_Helper_Data::URL_WELCOME_ID_KEY));

    }
}
