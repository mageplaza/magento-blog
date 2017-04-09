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
 * post - tag relation edit block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tab_Tag extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('tag_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getPost()->getId()) {
            $this->setDefaultFilter(array('in_tags' => 1));
        }
    }

    /**
     * prepare the tag collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tab_Tag
     * @author Sam
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('mageplaza_betterblog/tag_collection');
        if ($this->getPost()->getId()) {
            $constraint = 'related.post_id='.$this->getPost()->getId();
        } else {
            $constraint = 'related.post_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('mageplaza_betterblog/post_tag')),
            'related.tag_id=main_table.entity_id AND '.$constraint,
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
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tab_Tag
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
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tab_Tag
     * @author Sam
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_tags',
            array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_tags',
                'values'            => $this->_getSelectedTags(),
                'align'             => 'center',
                'index'             => 'entity_id'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('mageplaza_betterblog')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
                'renderer'  => 'mageplaza_betterblog/adminhtml_helper_column_renderer_relation',
                'params'    => array(
                    'id'    => 'getId'
                ),
                'base_link' => 'adminhtml/betterblog_tag/edit',
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
    protected function _getSelectedTags()
    {
        $tags = $this->getPostTags();
        if (!is_array($tags)) {
            $tags = array_keys($this->getSelectedTags());
        }
        return $tags;
    }

    /**
     * Retrieve selected {{siblingsLabels}}
     *
     * @access protected
     * @return array
     * @author Sam
     */
    public function getSelectedTags()
    {
        $tags = array();
        $selected = Mage::registry('current_post')->getSelectedTags();
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $tag) {
            $tags[$tag->getId()] = array('position' => $tag->getPosition());
        }
        return $tags;
    }

    /**
     * get row url
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Tag
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
            '*/*/tagsGrid',
            array(
                'id' => $this->getPost()->getId()
            )
        );
    }

    /**
     * get the current post
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
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Edit_Tab_Tag
     * @author Sam
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_tags') {
            $tagIds = $this->_getSelectedTags();
            if (empty($tagIds)) {
                $tagIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$tagIds));
            } else {
                if ($tagIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$tagIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
