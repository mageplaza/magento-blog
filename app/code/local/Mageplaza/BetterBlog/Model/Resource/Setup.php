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
 * BetterBlog setup
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
class Mageplaza_BetterBlog_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Setup
{

    /**
     * get the default entities for betterblog module - used at installation
     *
     * @access public
     * @return array()
     * @author Sam
     */
    public function getDefaultEntities()
    {
        $entities = array();
        $entities['mageplaza_betterblog_post'] = array(
            'entity_model' => 'mageplaza_betterblog/post',
            'attribute_model' => 'mageplaza_betterblog/resource_eav_attribute',
            'table' => 'mageplaza_betterblog/post',
            'additional_attribute_table' => 'mageplaza_betterblog/eav_attribute',
            'entity_attribute_collection' => 'mageplaza_betterblog/post_attribute_collection',
            'attributes' => array(
                'post_title' => array(
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Name',
                    'input' => 'text',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '1',
                    'user_defined' => false,
                    'default' => '',
                    'unique' => false,
                    'position' => '10',
                    'note' => 'Post name',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),
                'post_excerpt' => array(
                    'group' => 'General',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Short Description',
                    'input' => 'textarea',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '0',
                    'user_defined' => true,
                    'default' => '',
                    'unique' => false,
                    'position' => '20',
                    'note' => 'Short Description',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),
                'post_content' => array(
                    'group' => 'General',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Content',
                    'input' => 'textarea',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '0',
                    'user_defined' => true,
                    'default' => '',
                    'unique' => false,
                    'position' => '30',
                    'note' => 'Content',
                    'visible' => '1',
                    'wysiwyg_enabled' => '1',
                ),
                'image' => array(
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => 'mageplaza_betterblog/post_attribute_backend_image',
                    'frontend' => '',
                    'label' => 'Image',
                    'input' => 'image',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '',
                    'user_defined' => true,
                    'default' => '',
                    'unique' => false,
                    'position' => '40',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),
                'status' => array(
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Enabled',
                    'input' => 'select',
                    'source' => 'eav/entity_attribute_source_boolean',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '1',
                    'unique' => false,
                    'position' => '50',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),
                'url_key' => array(
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => 'mageplaza_betterblog/post_attribute_backend_urlkey',
                    'frontend' => '',
                    'label' => 'URL key',
                    'input' => 'text',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '',
                    'unique' => false,
                    'position' => '60',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),
                'in_rss' => array(
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'In RSS',
                    'input' => 'select',
                    'source' => 'eav/entity_attribute_source_boolean',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '1',
                    'unique' => false,
                    'position' => '70',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),
                'meta_title' => array(
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Meta title',
                    'input' => 'text',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '',
                    'unique' => false,
                    'position' => '80',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),
                'meta_keywords' => array(
                    'group' => 'General',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Meta keywords',
                    'input' => 'textarea',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '',
                    'unique' => false,
                    'position' => '90',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),
                'meta_description' => array(
                    'group' => 'General',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Meta description',
                    'input' => 'textarea',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '',
                    'unique' => false,
                    'position' => '100',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),
                'allow_comment' => array(
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Allow Comment',
                    'input' => 'select',
                    'source' => 'mageplaza_betterblog/adminhtml_source_yesnodefault',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '2',
                    'unique' => false,
                    'position' => '110',
                    'note' => '',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),

                'views' => array(
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Views',
                    'input' => 'text',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '1',
                    'unique' => false,
                    'position' => '91',
                    'note' => 'Post view',
                    'visible' => '0',
                    'wysiwyg_enabled' => '0',
                ),

                'comment_count' => array(
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Comment Count',
                    'input' => 'text',
                    'source' => '',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    'required' => '',
                    'user_defined' => false,
                    'default' => '0',
                    'unique' => false,
                    'position' => '92',
                    'note' => 'Comment Count',
                    'visible' => '0',
                    'wysiwyg_enabled' => '0',
                ),

                'topics' => array(
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Select a topic',
                    'input' => 'select',
                    'source' => 'eav/entity_attribute_source_table',
                    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                    'required' => '',
                    'user_defined' => true,
                    'default' => '',
                    'unique' => false,
                    'position' => '93',
                    'note' => 'You can add more topics at Blog > Post Attributes > topics > Manage Label/Options',
                    'visible' => '1',
                    'wysiwyg_enabled' => '0',
                ),



            )
        );
        return $entities;
    }

    public function insertDefaultBlocks()
    {
        if(version_compare(Mage::getVersion(),'1.9.2.1','>')){
            $blocks = array(
                'mageplaza_betterblog/post_recent',
                'mageplaza_betterblog/post_cat',
                'mageplaza_betterblog/post_list',
                'mageplaza_betterblog/post_view',
                'mageplaza_betterblog/category_list',
                'mageplaza_betterblog/tag_list',
            );
            foreach($blocks as $_block){
                $this->_saveBlock($_block);
            }
        }


    }

    protected function _saveBlock($name = '')
    {
        try {

            if (empty($name)) return;
            $data = array(
                'block_name' => $name,
                'is_allowed' => 1
            );
            $model = Mage::getModel('admin/block')->load($name);
            if ($model->getId()) {
                return;
            }
            $model->setData($data);
            $model->save();
        } catch (Exception $e) {
        }
    }

    public function initSetup()
    {
        Mage::getModel('mageplaza_betterblog/category')
            ->load(1)
            ->setParentId(0)
            ->setPath(1)
            ->setLevel(0)
            ->setPosition(0)
            ->setChildrenCount(0)
            ->setName('Root')
            ->setStoreId(0)
            ->setInitialSetupFlag(true)
            ->save();

        /**
         * Save default category
         */
        try {
            Mage::getModel('mageplaza_betterblog/category')
                ->setParentId(1)
                ->setPath('1/2')
                ->setLevel(1)
                ->setPosition(1)
                ->setChildrenCount(0)
                ->setName('News')
                ->setUrlKey('news')
                ->setInitialSetupFlag(true)
                ->setStatus(1)
                ->setStoreId(0)
                ->save();
        } catch (Exception $e) {
        }

        try {
            Mage::getModel('mageplaza_betterblog/category')
                ->setParentId(1)
                ->setPath('1/3')
                ->setLevel(1)
                ->setPosition(1)
                ->setChildrenCount(0)
                ->setName('Events')
                ->setUrlKey('events')
                ->setInitialSetupFlag(true)
                ->setStoreId(0)
                ->setStatus(1)
                ->save();
        } catch (Exception $e) {
        }


//        $post = Mage::getModel('mageplaza_betterblog/post');
//        $post->setData(
//            array(
//                'name'=>'Hello World',
//                'url_key'=>'hello-world',
//                'short_description'=>'Welcome to our blog. Thanks for using Mageplaza extensions. You can edit/delete this post in Admin > Blog > Manage Posts',
//                'status'=>1,
//                'allow_comment'=>2,
//                'image'=>'/w/e/welcome.png',
//            )
//        );
//        try {
//            $post->save();
//        } catch (Exception $e) {
//            Mage::log($e->getMessage());
//        }





        $baseUrl = Mage::getBaseUrl(true);
        $domain = parse_url($baseUrl, PHP_URL_HOST);;
        $content = '<h2>Introduction</h2>
<p>This is our blog at ' . $baseUrl . ', we will update blog frequently. You can follow this blog posts by subscribing our newsletter.</p>
<h2>What Is a Blog? - ' . $domain .'</h2>
<p>The term "blog" is short for "weblog," which refers to an online journal. <a href="https://www.magentocommerce.com/magento-connect/better-blog.html">Blogs</a> began as personal mini sites that people used to record their opinions, stories, and other writings as well as photos and videos.</p>
<p>As the web has grown and changed, blogs have gained more recognition and merit. Nowadays, blogs can be for&nbsp;businesses, news, networking, and other professional means. There are still plenty of personal blogs out there, but overall blogs are being taken much more seriously.</p>
<h2>How does the blog work</h2>
<p><strong>A blog extension is much simpler</strong></p>
<ul>
<li>A blog is normally a&nbsp;<strong>single page</strong>&nbsp;of entries. There may be archives of older entries, but the "main page" of a blog is all anyone really cares about.</li>
<li>A blog is organized in&nbsp;<strong>reverse-chronological order</strong>, from most recent entry to least recent.</li>
<li>A blog is normally&nbsp;<strong>public</strong>&nbsp;-- the whole world can see it.</li>
<li>The entries in a blog usually come from a&nbsp;<strong>single author</strong>.</li>
<li>The entries in a blog are usually&nbsp;<strong>stream-of-consciousness</strong>. There is no particular order to them. For example, if I see a good link, I can throw it in my blog. The tools that most bloggers use make it incredibly easy to add entries to a blog anytime they feel like it.</li>
</ul>
<p style="text-align: right;">Powered by <a href="https://www.magentocommerce.com/magento-connect/better-blog.html">Better Blog extension</a></p>';



        try {
            $key = Mageplaza_BetterBlog_Helper_Data::URL_WELCOME_ID_KEY;
            $page = Mage::getModel('cms/page')->loadByAttribute('identifier', $key);
            if(!$page){
                $page = Mage::getModel('cms/page');
            }
            $title = 'Welcome to our Blog';
            $page->setTitle($title)
                ->setContentHeading('Welcome to our Blog')
                ->setContent($content)
                ->setIdentifier('welcome-to-our-blog')
                ->setIsActive(true)
                ->save();
        } catch (Exception $e) {
        }

    }
}
