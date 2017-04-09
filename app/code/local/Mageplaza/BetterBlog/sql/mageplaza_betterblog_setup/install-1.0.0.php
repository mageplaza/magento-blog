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
 * BetterBlog module install script
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterBlog
 * @author      Sam
 */
$this->startSetup();
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/category'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Category ID'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable' => false,
        ),
        'Name'
    )
    ->addColumn(
        'description',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(),
        'Description'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(),
        'Enabled'
    )
    ->addColumn(
        'url_key',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(),
        'URL key'
    )
    ->addColumn(
        'parent_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
        ),
        'Parent id'
    )
    ->addColumn(
        'path',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(),
        'Path'
    )
    ->addColumn(
        'position',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
        ),
        'Position'
    )
    ->addColumn(
        'level',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
        ),
        'Level'
    )
    ->addColumn(
        'children_count',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
        ),
        'Children count'
    )
    ->addColumn(
        'in_rss',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(),
        'In RSS'
    )
    ->addColumn(
        'meta_title',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(),
        'Meta title'
    )
    ->addColumn(
        'meta_keywords',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(),
        'Meta keywords'
    )
    ->addColumn(
        'meta_description',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(),
        'Meta description'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Category Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Category Creation Time'
    )
    ->setComment('Category Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/tag'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Tag ID'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable' => false,
        ),
        'Name'
    )
    ->addColumn(
        'description',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(),
        'Description'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(),
        'Enabled'
    )
    ->addColumn(
        'url_key',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(),
        'URL key'
    )
    ->addColumn(
        'in_rss',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(),
        'In RSS'
    )
    ->addColumn(
        'meta_title',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(),
        'Meta title'
    )
    ->addColumn(
        'meta_keywords',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(),
        'Meta keywords'
    )
    ->addColumn(
        'meta_description',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(),
        'Meta description'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Tag Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Tag Creation Time'
    )
    ->setComment('Tag Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/category_store'))
    ->addColumn(
        'category_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable' => false,
            'primary' => true,
        ),
        'Category ID'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Store ID'
    )
    ->addIndex(
        $this->getIdxName(
            'mageplaza_betterblog/category_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/category_store',
            'category_id',
            'mageplaza_betterblog/category',
            'entity_id'
        ),
        'category_id',
        $this->getTable('mageplaza_betterblog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/category_store',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Categories To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/tag_store'))
    ->addColumn(
        'tag_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable' => false,
            'primary' => true,
        ),
        'Tag ID'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Store ID'
    )
    ->addIndex(
        $this->getIdxName(
            'mageplaza_betterblog/tag_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/tag_store',
            'tag_id',
            'mageplaza_betterblog/tag',
            'entity_id'
        ),
        'tag_id',
        $this->getTable('mageplaza_betterblog/tag'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/tag_store',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Tags To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/post'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Entity ID'
    )
    ->addColumn(
        'entity_type_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Entity Type ID'
    )
    ->addColumn(
        'attribute_set_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Attribute Set ID'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null, array(),
        'Creation Time'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Update Time'
    )
    ->addIndex(
        $this->getIdxName(
            'mageplaza_betterblog/post',
            array('entity_type_id')
        ),
        array('entity_type_id')
    )
    ->addIndex(
        $this->getIdxName(
            'mageplaza_betterblog/post',
            array('attribute_set_id')
        ),
        array('attribute_set_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id',
        $this->getTable('eav/attribute_set'),
        'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post',
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id',
        $this->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Post Table');
$this->getConnection()->createTable($table);

$postEav = array();
$postEav['int'] = array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'length' => null,
    'comment' => 'Post Datetime Attribute Backend Table'
);

$postEav['varchar'] = array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'Post Varchar Attribute Backend Table'
);

$postEav['text'] = array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '64k',
    'comment' => 'Post Text Attribute Backend Table'
);

$postEav['datetime'] = array(
    'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'length' => null,
    'comment' => 'Post Datetime Attribute Backend Table'
);

$postEav['decimal'] = array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'length' => '12,4',
    'comment' => 'Post Datetime Attribute Backend Table'
);

foreach ($postEav as $type => $options) {
    $table = $this->getConnection()
        ->newTable($this->getTable(array('mageplaza_betterblog/post', $type)))
        ->addColumn(
            'value_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity' => true,
                'nullable' => false,
                'primary' => true,
            ),
            'Value ID'
        )
        ->addColumn(
            'entity_type_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
            ),
            'Entity Type ID'
        )
        ->addColumn(
            'attribute_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
            ),
            'Attribute ID'
        )
        ->addColumn(
            'store_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
            ),
            'Store ID'
        )
        ->addColumn(
            'entity_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
            ),
            'Entity ID'
        )
        ->addColumn(
            'value',
            $options['type'],
            $options['length'], array(),
            'Value'
        )
        ->addIndex(
            $this->getIdxName(
                array('mageplaza_betterblog/post', $type),
                array('entity_id', 'attribute_id', 'store_id'),
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            array('entity_id', 'attribute_id', 'store_id'),
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
        )
        ->addIndex(
            $this->getIdxName(
                array('mageplaza_betterblog/post', $type),
                array('store_id')
            ),
            array('store_id')
        )
        ->addIndex(
            $this->getIdxName(
                array('mageplaza_betterblog/post', $type),
                array('entity_id')
            ),
            array('entity_id')
        )
        ->addIndex(
            $this->getIdxName(
                array('mageplaza_betterblog/post', $type),
                array('attribute_id')
            ),
            array('attribute_id')
        )
        ->addForeignKey(
            $this->getFkName(
                array('mageplaza_betterblog/post', $type),
                'attribute_id',
                'eav/attribute',
                'attribute_id'
            ),
            'attribute_id',
            $this->getTable('eav/attribute'),
            'attribute_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName(
                array('mageplaza_betterblog/post', $type),
                'entity_id',
                'mageplaza_betterblog/post',
                'entity_id'
            ),
            'entity_id',
            $this->getTable('mageplaza_betterblog/post'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName(
                array('mageplaza_betterblog/post', $type),
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id',
            $this->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment($options['comment']);
    $this->getConnection()->createTable($table);
}
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/post_comment'))
    ->addColumn(
        'comment_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Post Comment ID'
    )
    ->addColumn(
        'post_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('nullable' => false),
        'Post ID'
    )
    ->addColumn(
        'title',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Comment Title'
    )
    ->addColumn(
        'comment',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        '64k',
        array('nullable' => false),
        'Comment'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array('nullable' => false),
        'Comment status'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('nullable' => true),
        'Customer id'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Customer name'
    )
    ->addColumn(
        'email',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array('nullable' => false),
        'Customer email'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Post Comment Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Post Comment Creation Time'
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post_comment',
            'post_id',
            'mageplaza_betterblog/post',
            'entity_id'
        ),
        'post_id',
        $this->getTable('mageplaza_betterblog/post'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post_comment',
            'customer_id',
            'customer/entity',
            'entity_id'
        ),
        'customer_id',
        $this->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Post Comments Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/post_comment_store'))
    ->addColumn(
        'comment_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable' => false,
            'primary' => true,
        ),
        'Comment ID'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Store ID'
    )
    ->addIndex(
        $this->getIdxName(
            'mageplaza_betterblog/post_comment_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post_comment_store',
            'comment_id',
            'mageplaza_betterblog/post_comment',
            'comment_id'
        ),
        'comment_id',
        $this->getTable('mageplaza_betterblog/post_comment'),
        'comment_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post_comment_store',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Posts Comments To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/post_category'))
    ->addColumn(
        'rel_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Relation ID'
    )
    ->addColumn(
        'post_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Post ID'
    )
    ->addColumn(
        'category_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Category ID'
    )
    ->addColumn(
        'position',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => false,
            'default' => '0',
        ),
        'Position'
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post_category',
            'post_id',
            'mageplaza_betterblog/post',
            'entity_id'
        ),
        'post_id',
        $this->getTable('mageplaza_betterblog/post'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post_category',
            'category_id',
            'mageplaza_betterblog/post',
            'entity_id'
        ),
        'category_id',
        $this->getTable('mageplaza_betterblog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            'mageplaza_betterblog/post_category',
            array('post_id', 'category_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('post_id', 'category_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Post to Category Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/post_tag'))
    ->addColumn(
        'rel_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Relation ID'
    )
    ->addColumn(
        'post_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Post ID'
    )
    ->addColumn(
        'tag_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Tag ID'
    )
    ->addColumn(
        'position',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => false,
            'default' => '0',
        ),
        'Position'
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post_tag',
            'post_id',
            'mageplaza_betterblog/post',
            'entity_id'
        ),
        'post_id',
        $this->getTable('mageplaza_betterblog/post'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'mageplaza_betterblog/post_tag',
            'tag_id',
            'mageplaza_betterblog/post',
            'entity_id'
        ),
        'tag_id',
        $this->getTable('mageplaza_betterblog/tag'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            'mageplaza_betterblog/post_tag',
            array('post_id', 'tag_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('post_id', 'tag_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Post to Tag Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('mageplaza_betterblog/eav_attribute'))
    ->addColumn(
        'attribute_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Attribute ID'
    )
    ->addColumn(
        'is_global',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Attribute scope'
    )
    ->addColumn(
        'position',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Attribute position'
    )
    ->addColumn(
        'is_wysiwyg_enabled',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Attribute uses WYSIWYG'
    )
    ->addColumn(
        'is_visible',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(),
        'Attribute is visible'
    )
    ->setComment('BetterBlog attribute table');
$this->getConnection()->createTable($table);

$this->installEntities();


//$path = Mageplaza_BetterBlog_Helper_Data::URL_REWRITE_ID_PATH;
//$storeId = Mage::app()->getStore()->getId();
//$rewrite = Mage::getModel('core/url_rewrite')
//    ->loadByIdPath($path);
//if (!$rewrite->getId()) {
//    $rewrite->setIdPath($path);
//}
//$rewrite
//    ->setStoreId($storeId)
//    ->setRequestPath('blog')
//    ->setTargetPath('blog/post/index/');
//try {
//    $rewrite->save();
//} catch (Exception $e) {
//}





try {
    $this->insertDefaultBlocks();
    $this->initSetup();
} catch (Exception $e) {
    Mage::log($e->getMessage());
}


//$post = Mage::getModel('mageplaza_betterblog/post');
//$post->setData(
//        array(
//            'name'=>'Hello World',
//            'url_key'=>'hello-world',
//            'short_description'=>'Welcome to our blog. Thanks for using Mageplaza extensions. You can edit/delete this post in Admin > Blog > Manage Posts',
//            'status'=>1,
//            'allow_comment'=>2,
//            'image'=>'/w/e/welcome.png',
//            'store'=>array(0),
//        )
//    );
//try {
//    $post->save();
//} catch (Exception $e) {
//    Mage::log($e->getMessage());
//}

$this->endSetup();
