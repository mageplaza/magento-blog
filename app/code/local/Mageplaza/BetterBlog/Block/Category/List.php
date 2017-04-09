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
 * Category list block
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author Sam
 */
class Mageplaza_BetterBlog_Block_Category_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Sam
     */
    public function _construct()
    {
        parent::_construct();
        $categories = Mage::getResourceModel('mageplaza_betterblog/category_collection')
                         ->addStoreFilter(Mage::app()->getStore())
                         ->addFieldToFilter('status', 1);
        ;
        $categories->getSelect()->order('main_table.position');
        $this->setCategories($categories);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Mageplaza_BetterBlog_Block_Category_List
     * @author Sam
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getCategories()->addFieldToFilter('level', 1);
        if ($this->_getDisplayMode() == 0) {
            $pager = $this->getLayout()->createBlock(
                'page/html_pager',
                'mageplaza_betterblog.categories.html.pager'
            )
            ->setCollection($this->getCategories());
            $this->setChild('pager', $pager);
            $this->getCategories()->load();
        }
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Sam
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * get the display mode
     *
     * @access protected
     * @return int
     * @author Sam
     */
    protected function _getDisplayMode()
    {
        return Mage::getStoreConfigFlag('mageplaza_betterblog/category/tree');
    }

    /**
     * draw category
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Category
     * @param int $level
     * @return int
     * @author Sam
     */
    public function drawCategory($category, $level = 0)
    {
        $html = '';
        $recursion = $this->getRecursion();
        if ($recursion !== '0' && $level >= $recursion) {
            return '';
        }
        $storeIds = Mage::getResourceSingleton(
            'mageplaza_betterblog/category'
        )
        ->lookupStoreIds($category->getId());
        $validStoreIds = array(0, Mage::app()->getStore()->getId());
        if (!array_intersect($storeIds, $validStoreIds)) {
            return '';
        }
        if (!$category->getStatus()) {
            return '';
        }
        $children = $category->getChildrenCategories();
        $activeChildren = array();
        if ($recursion == 0 || $level < $recursion-1) {
            foreach ($children as $child) {
                $childStoreIds = Mage::getResourceSingleton(
                    'mageplaza_betterblog/category'
                )
                ->lookupStoreIds($child->getId());
                $validStoreIds = array(0, Mage::app()->getStore()->getId());
                if (!array_intersect($childStoreIds, $validStoreIds)) {
                    continue;
                }
                if ($child->getStatus()) {
                    $activeChildren[] = $child;
                }
            }
        }
        $html .= '<li>';
        $html .= '<a href="'.$category->getCategoryUrl().'">'.$category->getName().'</a>';
        if (count($activeChildren) > 0) {
            $html .= '<ul>';
            foreach ($children as $child) {
                $html .= $this->drawCategory($child, $level+1);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    /**
     * get recursion
     *
     * @access public
     * @return int
     * @author Sam
     */
    public function getRecursion()
    {
        if (!$this->hasData('recursion')) {
            $this->setData('recursion', Mage::getStoreConfig('mageplaza_betterblog/category/recursion'));
        }
        return $this->getData('recursion');
    }
}
