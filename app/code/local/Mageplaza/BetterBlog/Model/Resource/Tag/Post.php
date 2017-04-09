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
 * Tag - Post relation model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Tag_Post extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @return void
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Sam
     */
    protected function  _construct()
    {
        $this->_init('mageplaza_betterblog/tag_post', 'rel_id');
    }

    /**
     * Save tag - post relations
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Tag $tag
     * @param array $data
     * @return Mageplaza_BetterBlog_Model_Resource_Tag_Post
     * @author Sam
     */
    public function saveTagRelation($tag, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $adapter = $this->_getWriteAdapter();
        $bind    = array(
            ':tag_id'    => (int)$tag->getId(),
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), array('rel_id', 'post_id'))
            ->where('tag_id = :tag_id');

        $related   = $adapter->fetchPairs($select, $bind);
        $deleteIds = array();
        foreach ($related as $relId => $postId) {
            if (!isset($data[$postId])) {
                $deleteIds[] = (int)$relId;
            }
        }
        if (!empty($deleteIds)) {
            $adapter->delete(
                $this->getMainTable(),
                array('rel_id IN (?)' => $deleteIds)
            );
        }

        foreach ($data as $postId => $info) {
            $adapter->insertOnDuplicate(
                $this->getMainTable(),
                array(
                    'tag_id'      => $tag->getId(),
                    'post_id'     => $postId,
                    'position'      => @$info['position']
                ),
                array('position')
            );
        }
        return $this;
    }
}
