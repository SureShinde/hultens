<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Setup;

/**
 *
 */
class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{

    /**
     * @internal
     * @var \Mediastrategi\Unifaun\Model\ShippingmethodFactory
     */
    protected $_shippingmethodFactory;

    /**
     * @internal
     * @var \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
    protected $_salesSetupFactory;

    /**
     * @param \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingmethodFactory
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingmethodFactory,
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
    ) {
        $this->_shippingmethodFactory = $shippingmethodFactory;
        $this->_salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
            $salesSetup = $this->_salesSetupFactory->create(['setup' => $setup]);

            // Remove previous attributes
            $attributes = [
                'msunifaun_addons',
                'pick_up_location_id',
                'pick_up_location_name',
                'pick_up_location_address',
                'pick_up_location_zip_code',
                'pick_up_location_city',
                'pick_up_location_country',
            ];
            foreach ($attributes as $attr_to_remove) {
                $salesSetup->removeAttribute(
                    \Magento\Sales\Model\Order::ENTITY,
                    $attr_to_remove
                );
            }

            // Options for new attributes
            $options = [
                'type' => 'varchar',
                'visible' => false,
                'required' => false,
            ];
            // Add new order-attributes
            foreach ($attributes as $attribute) {
                $salesSetup->addAttribute(
                    'order',
                    $attribute,
                    $options
                );
            }
        }
        $setup->endSetup();
    }
}
