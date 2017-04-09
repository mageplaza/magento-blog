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
 * Category admin widget chooser
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */

class Mageplaza_BetterBlog_Block_Adminhtml_Category_Widget_Chooser extends Mageplaza_BetterBlog_Block_Adminhtml_Category_Tree
{
    protected $_selectedCategories = array();

    /**
     * Block construction
     * Defines tree template and init tree params
     *
     * @access public
     * @return void
     * @author Sam
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mageplaza_betterblog/category/widget/tree.phtml');
    }

    /**
     * Setter
     *
     * @access public
     * @param array $selectedCategories
     * @return Mageplaza_BetterBlog_Block_Adminhtml_Category_Widget_Chooser
     * @author Sam
     */
    public function setSelectedCategories($selectedCategories)
    {
        $this->_selectedCategories = $selectedCategories;
        return $this;
    }

    /**
     * Getter
     *
     * @access public
     * @return array
     * @author Sam
     */
    public function getSelectedCategories()
    {
        return $this->_selectedCategories;
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
            '*/betterblog_category_widget/chooser',
            array('uniq_id' => $uniqId, 'use_massaction' => false)
        );
        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);
        $value = $element->getValue();
        $categoryId = false;
        if ($value) {
            $categoryId = $value;
        }
        if ($categoryId) {
            $label = Mage::getSingleton('mageplaza_betterblog/category')->load($categoryId)
                ->getName();
            $chooser->setLabel($label);
        }
        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * onClick listener js function
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getNodeClickListener()
    {
        if ($this->getData('node_click_listener')) {
            return $this->getData('node_click_listener');
        }
        if ($this->getUseMassaction()) {
            $js = '
                function (node, e) {
                    if (node.ui.toggleCheck) {
                        node.ui.toggleCheck(true);
                    }
                }
            ';
        } else {
            $chooserJsObject = $this->getId();
            $js = '
                function (node, e) {
                    '.$chooserJsObject.'.setElementValue(node.attributes.id);
                    '.$chooserJsObject.'.setElementLabel(node.text);
                    '.$chooserJsObject.'.close();
                }
            ';
        }
        return $js;
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @access protected
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string
     * @author Sam
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = parent::_getNodeJson($node, $level);
        if (in_array($node->getId(), $this->getSelectedCategories())) {
            $item['checked'] = true;
        }
        return $item;
    }

    /**
     * Tree JSON source URL
     *
     * @access public
     * @param mixed $expanded
     * @return string
     * @author Sam
     */
    public function getLoadTreeUrl($expanded=null)
    {
        return $this->getUrl(
            '*/betterblog_category_widget/categoriesJson',
            array(
                '_current'=>true,
                'uniq_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction()
            )
        );
    }
}
