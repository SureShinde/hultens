<?php
/**
 *
 */
namespace Crealevant\Relevant\Setup;

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

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'cms_background_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => \Magento\Framework\Db\Ddl\Table::MAX_TEXT_SIZE,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Store Image for each CMS page',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'cms_background_description',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Store CMS Background Description for each CMS page',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'cms_background_link',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Store CMS Background Link for each CMS page',
                ]
            );
        }
        $setup->endSetup();
    }
}