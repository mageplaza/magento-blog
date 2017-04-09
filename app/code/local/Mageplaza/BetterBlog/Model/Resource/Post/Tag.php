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
 * Post - Tag relation model
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Post_Tag extends Mage_Core_Model_Resource_Db_Abstract
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
        $this->_init('mageplaza_betterblog/post_tag', 'rel_id');
    }

    /**
     * Save post - tag relations
     *
     * @access public
     * @param Mageplaza_BetterBlog_Model_Post $post
     * @param array $data
     * @return Mageplaza_BetterBlog_Model_Resource_Post_Tag
     * @author Sam
     */
    public function savePostRelation($post, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $adapter = $this->_getWriteAdapter();
        $bind    = array(
            ':post_id'    => (int)$post->getId(),
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), array('rel_id', 'tag_id'))
            ->where('post_id = :post_id');

        $related   = $adapter->fetchPairs($select, $bind);
        $deleteIds = array();
        foreach ($related as $relId => $tagId) {
            if (!isset($data[$tagId])) {
                $deleteIds[] = (int)$relId;
            }
        }
        if (!empty($deleteIds)) {
            $adapter->delete(
                $this->getMainTable(),
                array('rel_id IN (?)' => $deleteIds)
            );
        }

        foreach ($data as $tagId => $info) {
            $adapter->insertOnDuplicate(
                $this->getMainTable(),
                array(
                    'post_id'      => $post->getId(),
                    'tag_id'     => $tagId,
                    'position'      => @$info['position']
                ),
                array('position')
            );
        }
        return $this;
    }
}
