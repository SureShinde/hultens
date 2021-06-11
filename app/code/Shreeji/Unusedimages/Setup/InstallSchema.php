<?php

namespace Shreeji\Unusedimages\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();
        $table = $installer->getConnection()
                ->newTable($installer->getTable('shreeji_unusedimages'))
                ->addColumn(
                        'unusedimage_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id'
                )
                ->addColumn(
                'filename', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false, 'default' => ''], 'Image Path'
                );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }

}
