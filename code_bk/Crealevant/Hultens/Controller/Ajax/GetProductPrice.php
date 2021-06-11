<?php

namespace Crealevant\Hultens\Controller\Ajax;

use Magento\Framework\App\Action\Context;

class GetProductPrice extends \Magento\Framework\App\Action\Action
{
    private $priceHelper;
    private $typeInstance;
    private $productRepository;
    private $resultJsonFactory;
    private $hultenHelper;

    public function __construct(
        Context $context,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Bundle\Model\Product\Type $typeInstance,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Crealevant\Hultens\Helper\Data $hultenHelper
    ) {
        parent::__construct($context);
        $this->priceHelper = $priceHelper;
        $this->typeInstance = $typeInstance;
        $this->productRepository = $productRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->hultenHelper = $hultenHelper;
    }

    /**
     *
     * @return $this
     */
    public function execute()
    {
        $result = [];
        if ($this->getRequest()->getParam('products') && $this->getRequest()->getParam('qtys')) {
            $skus = explode(' - ', $this->getRequest()->getParam('products'));
            $qtys = explode(' - ', $this->getRequest()->getParam('qtys'));
            $totalPrices = 0;
            $isAppyRules = false;
            $isSpecialPrice = false;
            $idx = 0;
            $todaysDate = date("Y-m-d");
            foreach ($skus as $sku) {
                $productRepository = $this->productRepository->get($sku);
                $special = $productRepository->getSpecialPrice();
                $FromDate = $productRepository->getSpecialFromDate();
                $ToDate = $productRepository->getSpecialToDate();
                if ($this->hultenHelper->getPrice($productRepository, $productRepository->getPrice())) {
                    $isAppyRules = true;
                }
                if ($special) {
                    $specialprices = 0;
                    if (($todaysDate > $FromDate) && ($todaysDate < $ToDate)) {
                        $isSpecialPrice = true;
                    }
                }
                $totalPrices += $productRepository->getPrice() * $qtys[$idx];
                $idx++;
            }
        }
        if (($isAppyRules || $isSpecialPrice) && $totalPrices) {
            $result['show_original_price'] = 1;
            $result['original_price'] = $this->priceHelper->currency($totalPrices, true, false);
        }
        return $this->resultJsonFactory->create()->setData($result);
    }
}
