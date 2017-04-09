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
 * Post comment form block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author Sam
 */
class Mageplaza_BetterBlog_Block_Post_Comment_Form extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Sam
     */
    public function __construct()
    {
        $customerSession = Mage::getSingleton('customer/session');
        parent::__construct();
        $data =  Mage::getSingleton('customer/session')->getPostCommentFormData(true);
        $data = new Varien_Object($data);
        // add logged in customer name as nickname
        if (!$data->getName()) {
            $customer = $customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $data->setName($customer->getFirstname());
                $data->setEmail($customer->getEmail());
            }
        }
        $this->setAllowWriteCommentFlag(
            $customerSession->isLoggedIn() ||
            Mage::getStoreConfigFlag('mageplaza_betterblog/post/allow_guest_comment')
        );
        if (!$this->getAllowWriteCommentFlag()) {
            $this->setLoginLink(
                Mage::getUrl(
                    'customer/account/login/',
                    array(
                        Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME => Mage::helper('core')->urlEncode(
                            Mage::getUrl('*/*/*', array('_current' => true)) .
                            '#comment-form'
                        )
                    )
                )
            );
        }
        $this->setCommentData($data);
    }

    /**
     * get current post
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Post
     * @author Sam
     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }

    /**
     * get form action
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getAction()
    {
        return Mage::getUrl(
            'mageplaza_betterblog/post/commentpost',
            array('id' => $this->getPost()->getId())
        );
    }
}
