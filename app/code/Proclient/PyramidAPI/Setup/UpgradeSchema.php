<?php

namespace Proclient\PyramidAPI\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();
    if (version_compare($context->getVersion(), '2.2.0', '<')) {
      $setup->getConnection()->addColumn(
        $setup->getTable('sales_order'),
        'pyramid_orderno',
        [
          'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
          'length' => 32,
          'nullable' => true,
          'comment' => 'Ordernr i Pyramid'
        ]
      );
      $setup->getConnection()->addColumn(
        $setup->getTable('sales_order'),
        'pyramid_sync',
        [
          'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
          'nullable' => true,
          'comment' => 'Senaste sync mot Pyramid'
        ]
      );
      $setup->getConnection()->addColumn(
        $setup->getTable('catalog_product_entity'),
        'pyramid_artno',
        [
          'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
          'length' => 32,
          'nullable' => true,
          'comment' => 'Artikelkod i Pyramid'
        ]
      );
      $setup->getConnection()->addColumn(
        $setup->getTable('catalog_product_entity'),
        'pyramid_sync',
        [
          'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
          'nullable' => true,
          'comment' => 'Senaste sync mot Pyramid'
        ]
      );
    }

    if (version_compare($context->getVersion(), '2.5.0', '<')) {
      $setup->getConnection()->addColumn(
        $setup->getTable('sales_invoice'),
        'pyramid_sync',
        [
          'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
          'nullable' => true,
          'comment' => 'Senaste sync mot Pyramid'
        ]
      );
    }
    $setup->endSetup();
  }
}

?>
