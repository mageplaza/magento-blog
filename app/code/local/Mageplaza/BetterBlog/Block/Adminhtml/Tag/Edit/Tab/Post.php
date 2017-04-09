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
 * tag - post relation edit block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tab_Post extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access protected
     * @author Sam
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('post_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getTag()->getId()) {
            $this->setDefaultFilter(array('in_posts' => 1));
        }
    }

    /**
     * prepare the post collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tab_Post
     * @author Sam
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('mageplaza_betterblog/post_collection')->addAttributeToSelect('post_title');
        if ($this->getTag()->getId()) {
            $constraint = 'related.tag_id='.$this->getTag()->getId();
        } else {
            $constraint = 'related.tag_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('mageplaza_betterblog/tag_post')),
            'related.post_id=e.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * prepare mass action grid
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tab_Post
     * @author Sam
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tab_Post
     * @author Sam
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_posts',
            array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_posts',
                'values'            => $this->_getSelectedPosts(),
                'align'             => 'center',
                'index'             => 'entity_id'
            )
        );
        $this->addColumn(
            'post_title',
            array(
                'header'    => Mage::helper('mageplaza_betterblog')->__('Name'),
                'align'     => 'left',
                'index'     => 'post_title',
                'renderer'  => 'mageplaza_betterblog/adminhtml_helper_column_renderer_relation',
                'params'    => array(
                    'id'    => 'getId'
                ),
                'base_link' => 'adminhtml/betterblog_post/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('mageplaza_betterblog')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
    }

    /**
     * Retrieve selected 
     *
     * @access protected
     * @return array
     * @author Sam
     */
    protected function _getSelectedPosts()
    {
        $posts = $this->getTagPosts();
        if (!is_array($posts)) {
            $posts = array_keys($this->getSelectedPosts());
        }
        return $posts;
    }

    /**
     * Retrieve selected {{siblingsLabels}}
     *
     * @access protected
     * @return array
     * @author Sam
     */
    public function getSelectedPosts()
    {
        $posts = array();
        $selected = Mage::registry('current_tag')->getSelectedPosts();
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $post) {
            $posts[$post->getId()] = array('position' => $post->getPosition());
        }
        return $posts;
    }

    /**
     * get row url
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Post
     * @return string
     * @author Sam
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/postsGrid',
            array(
                'id' => $this->getTag()->getId()
            )
        );
    }

    /**
     * get the current tag
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Tag
     * @author Sam
     */
    public function getTag()
    {
        return Mage::registry('current_tag');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Edit_Tab_Post
     * @author Sam
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_posts') {
            $postIds = $this->_getSelectedPosts();
            if (empty($postIds)) {
                $postIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$postIds));
            } else {
                if ($postIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$postIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
