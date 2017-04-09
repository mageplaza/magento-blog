<?php

class Mageplaza_BetterBlog_Block_Comment extends Mage_Core_Block_Template {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Post_Comment_List
     * @author Sam
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }


    public function getCommentType()
    {
        $config = Mage::helper('mageplaza_betterblog/config');
        return $config->getCommentConfig('type');

    }
}
