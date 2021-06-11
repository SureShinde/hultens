<?php


namespace Crealevant\FeedManager\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_crealevant_feedmanager_feeds = $setup->getConnection()->newTable($setup->getTable('crealevant_feeds'));

        
        $table_crealevant_feedmanager_feeds->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );

        $table_crealevant_feedmanager_feeds->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'status'
        );

		$table_crealevant_feedmanager_feeds->addColumn(
			'export_category',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			[],
			'export_category'
		);

		$table_crealevant_feedmanager_feeds->addColumn(
			'file_type',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			[],
			'file_type'
		);

        $table_crealevant_feedmanager_feeds->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'name'
        );

        $table_crealevant_feedmanager_feeds->addColumn(
            'path',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'path'
        );

        $table_crealevant_feedmanager_feeds->addColumn(
            'exclude_out_stock',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Exclude out of stock product'
        );

        $table_crealevant_feedmanager_feeds->addColumn(
            'exclude_config_bundle',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Exclude configurable and bundle product'
        );

        $table_crealevant_feedmanager_feeds->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Store ID'
        );

        $table_crealevant_feedmanager_feed_attribute = $setup->getConnection()->newTable($setup->getTable('crealevant_feeds_attribute'));


        $table_crealevant_feedmanager_feed_attribute->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );


        $table_crealevant_feedmanager_feed_attribute->addColumn(
            'id_feed',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'id_feed'
        );


        $table_crealevant_feedmanager_feed_attribute->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'title'
        );


        $table_crealevant_feedmanager_feed_attribute->addColumn(
            'attribute_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'attribute_value'
        );
        

        $setup->getConnection()->createTable($table_crealevant_feedmanager_feed_attribute);

        $setup->getConnection()->createTable($table_crealevant_feedmanager_feeds);

        $setup->endSetup();
    }
}
