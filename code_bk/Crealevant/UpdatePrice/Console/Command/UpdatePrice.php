<?php

namespace Crealevant\UpdatePrice\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;

class UpdatePrice extends Command
{
    const STORE_CODE = "store_code";
    protected $productCollectionFactory;
    protected $productFactory;
    protected $storeManager;
    protected $state;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory,
        State $state,
        $name = null
    )
    {
        parent::__construct($name);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $argStoreCode = $input->getArgument(self::STORE_CODE);
        $storeCode = ($argStoreCode) ? $argStoreCode : 'dk';

        $stores = $this->storeManager->getStores(true, false);
        foreach($stores as $store){      
            if($store->getCode() === $storeCode){
                $storeId = $store->getId();
                break;
            }
        }

        if($storeId){
            $output->writeln("Collection");
           	$productCollection = $this->productCollectionFactory->create()->addAttributeToSelect('price')->addStoreFilter($storeId);
           	$total = $productCollection->getSize();
            $i = 0;    
            foreach ($productCollection as $product){
                $product_id = $product->getId();
                $prod = $this->productFactory->create()->setStoreId($storeId)->load($product_id);
                $currentPrice = $prod->getPrice();
                $newPrice = $currentPrice - $currentPrice * 0.1;
                $prod->setData('price', (float)$newPrice);
                $prod->getResource()->saveAttribute($prod, 'price');

               	$i++;  
               	$output->writeln($i . " / " . $total . "          | currentPrice: " . $currentPrice . " | newPrice: " . $newPrice);         
            }

            $output->writeln("Done");
        } else {
            $output->writeln("Not found store  by code {$storeCode}");
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {

        $this->setName("crealevant:updateprice");
        $this->setDescription("Update price product by store");
        $this->setDefinition([
            new InputArgument(self::STORE_CODE, InputArgument::OPTIONAL, "Store Code"),
        ]);
        parent::configure();
    }
}
