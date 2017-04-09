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
 * Tag admin grid block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Block_Adminhtml_Tag_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('tagGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Grid
     * @author Sam
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mageplaza_betterblog/tag')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Grid
     * @author Sam
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('mageplaza_betterblog')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('mageplaza_betterblog')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('mageplaza_betterblog')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('mageplaza_betterblog')->__('Enabled'),
                    '0' => Mage::helper('mageplaza_betterblog')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'url_key',
            array(
                'header' => Mage::helper('mageplaza_betterblog')->__('URL key'),
                'index'  => 'url_key',
            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'store_id',
                array(
                    'header'     => Mage::helper('mageplaza_betterblog')->__('Store Views'),
                    'index'      => 'store_id',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback'=> array($this, '_filterStoreCondition'),
                )
            );
        }
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('mageplaza_betterblog')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('mageplaza_betterblog')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('mageplaza_betterblog')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
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
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Grid
     * @author Sam
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('tag');
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
                'label'      => Mage::helper('mageplaza_betterblog')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('mageplaza_betterblog')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('mageplaza_betterblog')->__('Enabled'),
                            '0' => Mage::helper('mageplaza_betterblog')->__('Disabled'),
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
     * @param Mageplaza_BetterBlog_Model_Tag
     * @return string
     * @author Sam
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
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
     * after collection load
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Grid
     * @author Sam
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * filter store column
     *
     * @access protected
     * @param Mageplaza_BetterBlog_Model_Resource_Tag_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Tag_Grid
     * @author Sam
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addStoreFilter($value);
        return $this;
    }
}
