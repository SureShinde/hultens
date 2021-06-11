<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Setup;

/**
 *
 */
class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup,
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ms_unifaun_shippingmethod'),
                'automation_enable',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Shipping-method Automation Enable',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('ms_unifaun_shippingmethod'),
                'automation_package_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => 'PC',
                    'comment' => 'Shipping-method Automation Package Type',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('ms_unifaun_shippingmethod'),
                'automation_order_status_before',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => 'pending',
                    'comment' => 'Shipping-method Order Status to Automate',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('ms_unifaun_shippingmethod'),
                'automation_admin_username',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => 'admin',
                    'comment' => 'Shipping-method Automation Admin Username',
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ms_unifaun_shippingmethod'),
                'extra',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Shipping-method Extra',
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ms_unifaun_shippingmethod'),
                'stored_shipment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Shipping-method stored shipment flag',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('ms_unifaun_shipment'),
                'tracking_link',
                [
                    'type' => \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                    'length' => \Magento\Framework\Db\Ddl\Table::MAX_TEXT_SIZE,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Unifaun Tracking Link',
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.3.19', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('ms_unifaun_shippingmethod'),
                'customs_enabled',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Shipping-method Customs Enabled',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('ms_unifaun_shippingmethod'),
                'customs_documents',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Shipping-method Customs Documents',
                ]
            );
        }

        $setup->endSetup();
    }
}
