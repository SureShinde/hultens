<?php

namespace Crealevant\Hultens\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\File\Csv;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\State;

class ImportEanCode extends Command
{
    const PATH_FILE = 'path_file';

    /**
     * @var Csv
     */
    protected $csv;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /** @var State **/
    private $state;

    public function __construct(
        Csv $csv,
        ProductRepositoryInterface $productRepository,
        State $state,
        $name = null
    ) {
        $this->csv = $csv;
        $this->productRepository = $productRepository;
        $this->state = $state;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('crealevant:import_ean_code');
        $this->setDescription('Import EAN code product from csv file');
        $this->addOption(
            self::PATH_FILE,
            null,
            InputOption::VALUE_REQUIRED,
            'Path File'
        );
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
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        $file = 'var/import/ean-code.csv';
        if ($input->getOption(self::PATH_FILE)) {
            $file = $input->getOption(self::PATH_FILE);
        }

        $csvData = $this->csv->getData($file);

        $totals = count($csvData) - 1;
        $totalSuccess = 0;
        $totalFail = 0;

        foreach ($csvData as $row => $data) {
            if ($row > 0) {
                $sku = $data[0];
                $eanCode = $data[1];

                try {
                    $product = $this->productRepository->get($sku);
                    $product->setEanKod($eanCode);
                    $product->getResource()->saveAttribute($product, 'ean_kod');
                    $output->writeln('<info>Update product ' . $sku . '</info>');
                    $totalSuccess++;
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $output->writeln('<comment>Can\'t find product ' . $sku . '</comment>');
                    $totalFail++;
                } catch (\Exception $e) {
                    $output->writeln('<error>' . $e->getMessage() . '</error>');
                }
            }
        }

        $output->writeln("---------");
        $output->writeln("Total success: " . $totalSuccess);
        $output->writeln("Total fail: " . $totalFail);
        $output->writeln("Total rows: " . $totals);
    }
}
