<?php

namespace Crealevant\Relevant\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Crealevant\Relevant\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        $connection->addColumn('cms_page', 'cms_background_image',['type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'comment' => 'Store Image for each CMS page']);
        $connection->addColumn('cms_page','cms_background_description',['type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'comment' => 'Store CMS Background Description for each CMS page']);
        $connection->addColumn('cms_page','cms_background_link',['type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'comment' => 'Store CMS Background Link for each CMS page']);
        $installer->endSetup();
    }
}