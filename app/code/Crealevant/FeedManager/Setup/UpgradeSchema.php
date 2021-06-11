<?php


namespace Crealevant\FeedManager\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
			$table_crealevant_feedmanager_feeds = $setup->getTable('crealevant_feeds');

			$connection = $setup->getConnection();

			$connection->addColumn(
				$table_crealevant_feedmanager_feeds,
				'export_category',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'comment' => "export_category"
				]
			);

			$connection->addColumn(
				$table_crealevant_feedmanager_feeds,
				'file_type',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'comment' => "file_type"
				]
			);
        }
        else if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $table_crealevant_feedmanager_feeds = $setup->getTable('crealevant_feeds');

            $connection = $setup->getConnection();

            $connection->addColumn(
                $table_crealevant_feedmanager_feeds,
                'exclude_out_stock',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'comment' => "Exclude out of stock product"
                ]
            );
        }
        else if (version_compare($context->getVersion(), "1.0.3", "<")) {
            $table_crealevant_feedmanager_feeds = $setup->getTable('crealevant_feeds');

            $connection = $setup->getConnection();

            $connection->addColumn(
                $table_crealevant_feedmanager_feeds,
                'exclude_config_bundle',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'comment' => "Exclude configurable and bundle product"
                ]
            );
        }
        else if (version_compare($context->getVersion(), "1.0.4", "<")) {
            $table_crealevant_feedmanager_feeds = $setup->getTable('crealevant_feeds');

            $connection = $setup->getConnection();

            $connection->addColumn(
                $table_crealevant_feedmanager_feeds,
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'comment' => "Store ID"
                ]
            );
        }

        $setup->endSetup();
    }
}
