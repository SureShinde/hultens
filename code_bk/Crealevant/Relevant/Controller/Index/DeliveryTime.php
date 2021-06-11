<?php

namespace Crealevant\Relevant\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class DeliveryTime extends Action
{
    protected $resultJsonFactory;
    private $productRepository;

    /**
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */

    public function execute()
    {

        $sku = $this->getRequest()->getParam('sku');
        if($sku) {
            $_product = $this->productRepository->get($sku);
            $greenStat = intval($_product->getData('inventory_green_status_min')); // Returns int(0)
            $redStat = intval($_product->getData('inventory_red_status_max')); // Returns int(10)
            $yellowTxt = $_product->getResource()->getAttribute('inventory_yellow_status_text')->getFrontend()->getValue($_product); // Returns: string(46) "Fåtal kvar i lager. Leverans 2-7 arbetsdagar."
            $redTxt = $_product->getResource()->getAttribute('inventory_red_status_text')->getFrontend()->getValue($_product); // Returns: string(25) "Leverans 3-10 arbetsdagar"
            $greenTxt = $_product->getResource()->getAttribute('inventory_green_status_text')->getFrontend()->getValue($_product); // Returns: string(25) "Leverans 3-10 arbetsdagar"

            $qtyStock = $_product->getExtensionAttributes()->getStockItem()->getQty();

            if ($qtyStock >= $greenStat):
                $result = $greenTxt;
            elseif ($qtyStock <= $redStat):
                $result = $redTxt;
            else:
                $result = $yellowTxt;
            endif;

        } else {
            $result = "";
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'success' => $result
        ]);
    }
}