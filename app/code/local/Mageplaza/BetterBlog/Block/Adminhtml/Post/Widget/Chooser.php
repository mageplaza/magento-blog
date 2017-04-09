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
 * Post admin widget chooser
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */

class Mageplaza_BetterBlog_Block_Adminhtml_Post_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Block construction, prepare grid params
     *
     * @access public
     * @param array $arguments Object data
     * @return void
     * @author Sam
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('chooser_status' => '1'));
    }

    /**
     * Prepare chooser element HTML
     *
     * @access public
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     * @author Sam
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl(
            '*/betterblog_post_widget/chooser',
            array('uniq_id' => $uniqId)
        );
        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);
        if ($element->getValue()) {
            $post = Mage::getModel('mageplaza_betterblog/post')->load($element->getValue());
            if ($post->getId()) {
                $chooser->setLabel($post->getPostTitle());
            }
        }
        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var postId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var postTitle = trElement.down("td").next().innerHTML;
                '.$chooserJsObject.'.setElementValue(postId);
                '.$chooserJsObject.'.setElementLabel(postTitle);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare a static blocks collection
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Widget_Chooser
     * @author Sam
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mageplaza_betterblog/post')->getCollection();
        $collection->addAttributeToSelect('post_title');
        $collection->addAttributeToSelect('status');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for the a grid
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Post_Widget_Chooser
     * @author Sam
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'chooser_id',
            array(
                'header' => Mage::helper('mageplaza_betterblog')->__('Id'),
                'align'  => 'right',
                'index'  => 'entity_id',
                'type'   => 'number',
                'width'  => 50
            )
        );

        $this->addColumn(
            'chooser_post_title',
            array(
                'header' => Mage::helper('mageplaza_betterblog')->__('Name'),
                'align'  => 'left',
                'index'  => 'post_title',
            )
        );
        $this->addColumn(
            'chooser_status',
            array(
                'header'  => Mage::helper('mageplaza_betterblog')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    0 => Mage::helper('mageplaza_betterblog')->__('Disabled'),
                    1 => Mage::helper('mageplaza_betterblog')->__('Enabled')
                ),
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * get url for grid
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'adminhtml/betterblog_post_widget/chooser',
            array('_current' => true)
        );
    }
}
