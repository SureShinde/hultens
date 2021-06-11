<?php

namespace Crealevant\Hultens\Console\Command;

use Magento\Framework\App\ResourceConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanDuplicateValue extends Command
{
    /**
     * @var ResourceConnection
     */
    protected $_resourceConnection;

    public function __construct(
        $name = null,
        ResourceConnection $resourceConnection
    )
    {
        $this->_resourceConnection = $resourceConnection;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('crealevant:remove:duplicate');
        $this->setDescription('Clean duplicate values from catalog_product_entity_varchar table');
        parent::configure();
    }

    /**
     * CLI command description
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $table = 'catalog_product_entity_varchar';
            $connection = $this->_resourceConnection->getConnection();
            $attributeId = 163;
            $select = $connection->select()->from(
                $table
            )->where(
                'attribute_id = :attribute_id'
            );
            $bind = [':attribute_id' => $attributeId];
            $stmt = $connection->query($select, $bind);
            $count = 0;
            foreach ($stmt->fetchAll() as $row) {
                $values = explode(',', $row['value']);
                if (isset($values) && count($values) > 1) {
                    echo "\n" . $row['value'];
                    // fix multiselect attributes contains duplicated values;
                    $values = array_unique($values);
                    $row['value'] = implode(",", $values);
                    echo "\n\n" . $row['value'] . "\n";
                    $this->updateData($table, $row, 'value_id', $output);
                    $count++;
                }
            }
            echo "\n Total updated: {$count}\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /*
     * string $tablename
     * []     $arrValues
     * string $field
     */
    private function updateData($tableName, $arrValues, $primaryField, $output)
    {
        if (is_array($arrValues) && $keyId = $arrValues[$primaryField]) {
            //update exist value
            $tableName = $this->_resourceConnection->getTableName($tableName);
            $connection = $this->_resourceConnection->getConnection();
            $bind = $arrValues;
            $where = ["{$primaryField}=?" => $keyId];
            $connection->update(
                $tableName,
                $bind,
                $where
            );
            $output->writeln(sprintf("<info>UPATE %s %s2 </info> \n", $tableName, $keyId));
        } else {
            $output->writeln(sprintf("<error>UPATE %s Fail!!!</error> \n", $tableName));
        }
    }
}
