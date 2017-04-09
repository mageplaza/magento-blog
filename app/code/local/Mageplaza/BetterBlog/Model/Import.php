<?php

class Mageplaza_BetterBlog_Model_Import
{


    /**
     * process import aw blog
     */
    public function aw()
    {
        /**
         * Import categories
         */

        $categories = Mage::getModel('blog/cat')->getCollection();
        $parent = Mage::getModel('mageplaza_betterblog/category')->load(1);
        foreach ($categories as $_cat) {
            $data = array(
                'name' => $_cat->getData('title'),
                'url_key' => $_cat->getData('identifier'),
                'meta_keywords' => $_cat->getData('meta_keywords'),
                'meta_description' => $_cat->getData('meta_description'),
                'status' => 0,
                'parent_id' => 1,
                'level' => 1,
            );
            $category = Mage::getModel('mageplaza_betterblog/category');
            $category->setData($data);
            try {
                $category->save();
                $category->setPath('1' . $category->getPath());
                $category->save();
                $newCatId = $category->getId();
                $posts = $this->_getAllPostsInCategory($_cat->getCatId());
                foreach ($posts as $_post) {
                    var_dump($_post->getData('post_content'));
                    die;
                    $model = Mage::getModel('mageplaza_betterblog/post');
                    $_postData = array(
                        'post_title' => $_post->getData('title'),
                        'post_content' => $_post->getData('post_content'),
                        'post_excerpt' => $_post->getData('short_content'),
                        'url_key' => $_post->getData('identifier'),
                        'created_at' => $_post->getData('created_time'),
                        'updated_at' => $_post->getData('update_time'),
                        'status' => 2,
                        'meta_keywords' => $_post->getData('meta_keywords'),
                        'meta_description' => $_post->getData('meta_description'),
                    );

                    $model->setData($_postData);
                    $model->setAttributeSetId($model->getDefaultAttributeSetId());

                    $model->setCategoriesData(array($newCatId));
                    $model->save();
                    $this->_insertTags($_post->getTags(), $model);
                    $comments = $_post->getComments();
                    $this->_insertComments($comments, $model);
                }
            } catch (Exception $e) {
                Mage::log('Cannot save category #' . $data['name'] . '. ' . $e->getMessage());
            }
        }
    }

    /**
     * get all posts in category id
     * @param $id
     * @return mixed
     */
    protected function _getAllPostsInCategory($id)
    {
        $collection = Mage::getModel('blog/blog')->getCollection()
//            ->addPresentFilter()
//            ->addEnableFilter(AW_Blog_Model_Status::STATUS_ENABLED)
//            ->addStoreFilter()
            ->joinComments();
        $collection->addCatFilter($id);
        return $collection;
    }

    /**
     * Insert tags
     * @param $tags
     * @param $post
     */
    protected function _insertTags($tags, $post)
    {
        $tagArray = array();
        $tags = implode(',', $tags);
        foreach ($tags as $_tag) {
            $model = Mage::getModel('mageplaza_betterblog/tag')->getCollection()
                ->addFieldToFilter('name', $_tag)
                ->getFirstItem();
            if ($model && $model->getId()) {
                $tagArray[$model->getId()] = array(
                    'position' => ''
                );
            } else {
                $model->setData(
                    array(
                        'name' => $_tag,
                        'status' => 1,
                        'created_at' => Mage::helper('core')->formatDate(now())
                    )
                );
                $model->save();
                if ($model && $model->getId()) {
                    $tagArray[$model->getId()] = array(
                        'position' => ''
                    );
                }
            }

        }
        $post->setTagsData($tagArray);
        try {
            $post->save();
        } catch (Exception $e) {
        }

    }

    protected function _insertComments($comments, $post)
    {


    }
}