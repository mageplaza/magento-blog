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
 * Tag - Post relation resource model collection
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Tag_Post_Collection extends Mageplaza_BetterBlog_Model_Resource_Post_Collection
{
    /**
     * remember if fields have been joined
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return Mageplaza_BetterBlog_Model_Resource_Tag_Post_Collection
     * @author Sam
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('mageplaza_betterblog/tag_post')),
                'related.post_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add tag filter
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Tag | int $tag
     * @return Mageplaza_BetterBlog_Model_Resource_Tag_Post_Collection
     * @author Sam
     */
    public function addTagFilter($tag)
    {
        if ($tag instanceof Mageplaza_BetterBlog_Model_Tag) {
            $tag = $tag->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.tag_id = ?', $tag);
        return $this;
    }
}
