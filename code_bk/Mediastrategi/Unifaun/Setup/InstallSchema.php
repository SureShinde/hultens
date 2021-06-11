<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Setup;

/**
 *
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('ms_unifaun_shippingmethod'))
            ->addColumn(
                'id',
                \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ],
                'Shipping-method Id'
            )->addColumn(
                'store',
                \Magento\Framework\Db\Ddl\Table::TYPE_SMALLINT,
                255,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0',
                ],
                'Shipping-method Store ID'
            )->addColumn(
                'title',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Shipping-method Title'
            )->addColumn(
                'method',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Shipping-method Method'
            )->addColumn(
                'active',
                \Magento\Framework\Db\Ddl\Table::TYPE_SMALLINT,
                255,
                [
                    'nullable' => false,
                    'default' => '0',
                ],
                'Shipping-method Active'
            )->addColumn(
                'pickup',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '0',
                ],
                'Shipping-method Custom Pick-Up Location'
            )->addColumn(
                'automation_enable',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                10,
                [
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Shipping-method Automation Enable',
                ]
            )->addColumn(
                'automation_package_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => 'PC',
                    'comment' => 'Shipping-method Automation Package Type',
                ]
            )->addColumn(
                'automation_order_status_before',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => 'pending',
                    'comment' => 'Shipping-method Order Status to Automate',
                ]
            )->addColumn(
                'automation_admin_username',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => 'admin',
                    'comment' => 'Shipping-method Automation Admin Username',
                ]
            )->addColumn(
                'customs_enabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                10,
                [
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Shipping-method Customs Enabled',
                ]
            )->addColumn(
                'customs_documents',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Shipping-method Customs Documents',
                ]
            )->addColumn(
                'options',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                '2M',
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Shipping-method Options'
            )->addColumn(
                'addons',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                '2M',
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Shipping-method Addons'
            )->addColumn(
                'extra',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                '2M',
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Shipping-method Extra'
            )->addColumn(
                'stored_shipment',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                10,
                [
                    'nullable' => false,
                    'default' => 0,
                ],
                'Shipping-method stored shipment flag'
            )->addColumn(
                'creation_time',
                \Magento\Framework\Db\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\Db\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Shipping-method Creation Time'
            )->setComment(
                'Mediastrategi Unifaun Shipping-methods Table'
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('ms_unifaun_shipment'))
            ->addColumn(
                'id',
                \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ],
                'Tracking ID'
            )->addColumn(
                'order_id',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Magento Order ID'
            )->addColumn(
                'partner_id',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun Partner ID (carrier)'
            )->addColumn(
                'href',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun href'
            )->addColumn(
                'shipment_id',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun id'
            )->addColumn(
                'status',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun status'
            )->addColumn(
                'shipment_no',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun shipmentNo'
            )->addColumn(
                'order_no',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun orderNo'
            )->addColumn(
                'reference',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun reference'
            )->addColumn(
                'service_id',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun serviceId'
            )->addColumn(
                'parcel_count',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun parcelCount'
            )->addColumn(
                'snd_name',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun sndName'
            )->addColumn(
                'snd_zipcode',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun sndZipcode'
            )->addColumn(
                'snd_city',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun sndCity'
            )->addColumn(
                'snd_country',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun sndCountry'
            )->addColumn(
                'rcv_name',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun rcvName'
            )->addColumn(
                'rcv_zipcode',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun rcvZipcode'
            )->addColumn(
                'rcv_city',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun rcvCity'
            )->addColumn(
                'rcv_country',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun rcvCountry'
            )->addColumn(
                'rcv_country',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun rcvCountry'
            )->addColumn(
                'created',
                \Magento\Framework\Db\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\Db\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Unifaun created'
            )->addColumn(
                'changed',
                \Magento\Framework\Db\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\Db\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Unifaun changed'
            )->addColumn(
                'ship_date',
                \Magento\Framework\Db\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\Db\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Unifaun shipDate'
            )->addColumn(
                'return_shipment',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun returnShipment'
            )->addColumn(
                'normal_shipment',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun normalShipment'
            )->addColumn(
                'consolidated',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun consolidated'
            )->addColumn(
                'parcels',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                \Magento\Framework\Db\Ddl\Table::MAX_TEXT_SIZE,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun parcels array'
            )->addColumn(
                'pdfs',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                \Magento\Framework\Db\Ddl\Table::MAX_TEXT_SIZE,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun pdfs array'
            )->addColumn(
                'previous_pdfs',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                \Magento\Framework\Db\Ddl\Table::MAX_TEXT_SIZE,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun previousPdfs array'
            )->addColumn(
                'tracking_link',
                \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                \Magento\Framework\Db\Ddl\Table::MAX_TEXT_SIZE,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Unifaun Tracking Link'
            )->addColumn(
                'created_at',
                \Magento\Framework\Db\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\Db\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Tracking Creation Time'
            )->setComment(
                'Mediastrategi Unifaun Shipment Table'
            );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
