<?php
/**
 *
 */

namespace Mediastrategi\Unifaun\Setup;

/**
 *
 */
class Recurring implements
    \Magento\Framework\Setup\InstallSchemaInterface
{

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup2
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
    
        $upgradeSchema = new UpgradeSchema();
        $upgradeSchema->upgrade($setup, $context);
    }
}
