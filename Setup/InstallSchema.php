<?php

namespace DR\Gallery\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'dr_gallery_gallery'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('dr_gallery_gallery')
        )->addColumn(
            'gallery_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Gallery ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Gallery Name'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Gallery Status'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Gallery Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Gallery Updated At'
        )->setComment(
            'Gallery Table'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'dr_gallery_image'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('dr_gallery_image')
        )->addColumn(
            'image_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Image ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Image Name'
        )->addColumn(
            'path',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Image Path'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Image Status'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Image Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Image Updated At'
        )->setComment(
            'Image Table'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'dr_gallery_gallery_image'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('dr_gallery_gallery_image')
        )->addColumn(
            'gallery_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Gallery ID'
        )->addColumn(
            'image_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Image ID'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Position'
        )->addForeignKey(
            $installer->getFkName('dr_gallery_gallery_image', 'gallery_id', 'dr_gallery_gallery', 'gallery_id'),
            'gallery_id',
            $installer->getTable('dr_gallery_gallery'),
            'gallery_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('dr_gallery_gallery_image', 'image_id', 'dr_gallery_image', 'image_id'),
            'image_id',
            $installer->getTable('dr_gallery_image'),
            'image_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Gallery To Image Linkage Table'
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
