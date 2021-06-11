<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 *
 */
class Uninstall implements UninstallInterface
{

    /**
     *
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
    
        $installer = $setup;
        $installer->startSetup();
        $installer->getConnection()->dropTable($installer->getTable('ms_unifaun_shippingmethod'));
        $installer->endSetup();
    }
}
