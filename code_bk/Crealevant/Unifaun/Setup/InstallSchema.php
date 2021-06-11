<?php

namespace Crealevant\Unifaun\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('ms_unifaun_shippingmethod'),
            'description',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => \Magento\Framework\Db\Ddl\Table::MAX_TEXT_SIZE,
                'nullable' => true,
                'default' => '',
                'comment' => 'Shipping-method Description',
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('ms_unifaun_shippingmethod'),
            'image',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'Shipping-method Image',
            ]
        );

        $setup->endSetup();
    }
}
