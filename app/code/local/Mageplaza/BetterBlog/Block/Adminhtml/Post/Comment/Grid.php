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
 * Post comments admin grid block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Post_Comment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Sam
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('postCommentGrid');
        $this->setDefaultSort('ct_comment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Comment_Grid
     * @author Sam
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('mageplaza_betterblog/post_comment_post_collection');
        $collection->addStoreData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Comment_Grid
     * @author Sam
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'ct_comment_id',
            array(
                'header'        => Mage::helper('mageplaza_betterblog')->__('Id'),
                'index'         => 'ct_comment_id',
                'type'          => 'number',
                'filter_index'  => 'ct.comment_id',
            )
        );
        $this->addColumn(
            'post_title',
            array(
                'header'        => Mage::helper('mageplaza_betterblog')->__('Name'),
                'index'         => 'post_title',
                'filter_index'  => 'post_title',
            )
        );
        $this->addColumn(
            'ct_title',
            array(
                'header'        => Mage::helper('mageplaza_betterblog')->__('Comment Title'),
                'index'         => 'ct_title',
                'filter_index'  => 'ct.title',
            )
        );
        $this->addColumn(
            'ct_name',
            array(
                'header'        => Mage::helper('mageplaza_betterblog')->__('Poster name'),
                'index'         => 'ct_name',
                'filter_index'  => 'ct.name',
            )
        );
        $this->addColumn(
            'ct_email',
            array(
                'header'        => Mage::helper('mageplaza_betterblog')->__('Poster email'),
                'index'         => 'ct_email',
                'filter_index'  => 'ct.email',
            )
        );
        $this->addColumn(
            'ct_status',
            array(
                'header'        => Mage::helper('mageplaza_betterblog')->__('Status'),
                'index'         => 'ct_status',
                'filter_index'  => 'ct.status',
                'type'          => 'options',
                'options'       => array(
                    Mageplaza_BetterBlog_Model_Post_Comment::STATUS_PENDING  =>
                        Mage::helper('mageplaza_betterblog')->__('Pending'),
                    Mageplaza_BetterBlog_Model_Post_Comment::STATUS_APPROVED =>
                        Mage::helper('mageplaza_betterblog')->__('Approved'),
                    Mageplaza_BetterBlog_Model_Post_Comment::STATUS_REJECTED =>
                        Mage::helper('mageplaza_betterblog')->__('Rejected'),
                )
            )
        );
        $this->addColumn(
            'ct_created_at',
            array(
                'header'        => Mage::helper('mageplaza_betterblog')->__('Created at'),
                'index'         => 'ct_created_at',
                'width'         => '120px',
                'type'          => 'datetime',
                'filter_index'  => 'ct.created_at',
            )
        );
        $this->addColumn(
            'ct_updated_at',
            array(
                'header'        => Mage::helper('mageplaza_betterblog')->__('Updated at'),
                'index'         => 'ct_updated_at',
                'width'         => '120px',
                'type'          => 'datetime',
                'filter_index'  => 'ct.updated_at',
            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'stores',
                array(
                    'header'     => Mage::helper('mageplaza_betterblog')->__('Store Views'),
                    'index'      => 'stores',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback' => array($this, '_filterStoreCondition'),
                )
            );
        }
        $this->addColumn(
            'action',
            array(
                'header'  => Mage::helper('mageplaza_betterblog')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getCtCommentId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('mageplaza_betterblog')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('mageplaza_betterblog')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('mageplaza_betterblog')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('mageplaza_betterblog')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Grid
     * @author Sam
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ct_comment_id');
        $this->setMassactionIdFilter('ct.comment_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('comment');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('mageplaza_betterblog')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('mageplaza_betterblog')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => Mage::helper('mageplaza_betterblog')->__('Change status'),
                'url'   => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                            'name' => 'status',
                            'type' => 'select',
                            'class' => 'required-entry',
                            'label' => Mage::helper('mageplaza_betterblog')->__('Status'),
                            'values' => array(
                                Mageplaza_BetterBlog_Model_Post_Comment::STATUS_PENDING  =>
                                    Mage::helper('mageplaza_betterblog')->__('Pending'),
                                Mageplaza_BetterBlog_Model_Post_Comment::STATUS_APPROVED =>
                                    Mage::helper('mageplaza_betterblog')->__('Approved'),
                                Mageplaza_BetterBlog_Model_Post_Comment::STATUS_REJECTED =>
                                    Mage::helper('mageplaza_betterblog')->__('Rejected'),
                            )
                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Post_Comment
     * @return string
     * @author Sam
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getCtCommentId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * filter store column
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Resource_Post_Comment_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Comment_Grid
     * @author Sam
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->setStoreFilter($value);
        return $this;
    }
}
