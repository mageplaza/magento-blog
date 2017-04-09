<?php

class Mageplaza_BetterBlog_Block_Sidebar extends Mage_Core_Block_Template{

    /**
     * initialize
     *
     * @access public
     * @author Sam
     */
    public function _construct()
    {
        parent::_construct();
        $config = Mage::helper('mageplaza_betterblog/config');
        $posts = Mage::getResourceModel('mageplaza_betterblog/post_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1);
        $posts->setOrder('created_at', 'desc');
        $count = $this->getPostCount() ? $this->getPostCount() : $config->getPostConfig('number_recent_posts');
        $posts->setPageSize($count);
        $this->setPosts($posts);

        if($config->getSidebarConfig('enable_mostview')){
            $mostviews = Mage::getResourceModel('mageplaza_betterblog/post_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', 1);
            $mostviews->setOrder('views', 'desc');
            $count = $config->getSidebarConfig('number_mostview_posts') ? $config->getSidebarConfig('number_mostview_posts') : 5;
            $mostviews->setPageSize($count);
            $this->setMostviews($mostviews);
        }
        if(Mage::helper('mageplaza_betterblog')->canShowCommentWidget()){
            $comments = Mage::getResourceModel('mageplaza_betterblog/post_comment_collection')
                ->addFieldToFilter('status', Mageplaza_BetterBlog_Model_Post_Comment::STATUS_APPROVED);
            $comments->setOrder('created_at', 'desc');
            $count = $config->getSidebarConfig('number_comment') ? $config->getSidebarConfig('number_comment') : 5;
            $comments->setPageSize($count);
            $this->setComments($comments);
        }


        $categories = Mage::getResourceModel('mageplaza_betterblog/category_collection')
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('level', 1)
        ;
        $categories->getSelect()->order('main_table.position');
        $this->setCategories($categories);


        $tags = Mage::getResourceModel('mageplaza_betterblog/tag_collection')
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('status', 1);
        $tags->setOrder('name', 'asc');
        $this->setTags($tags);

    }


}