<?php

namespace Crealevant\ProductDeliveryDate\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Crealevant\ProductDeliveryDate\Helper\Data as Helper;

class Index extends Action
{
    protected $resultJsonFactory;

    protected $productRepository;

    protected $helper;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ProductRepositoryInterface $productRepository,
        Helper $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $skus = $this->getRequest()->getParam('sku');
        $shippingInfoType = 0;
        $shippingInfoText = '';

        if (!is_null($skus)) {
            foreach ($skus as $sku) {
                $product = $this->productRepository->get($sku);
                $shippingInfo = $this->helper->getTextShippingInfo($product);
                if ($shippingInfo['type'] == $shippingInfoType) {
                    if (strcmp($shippingInfoText, $shippingInfo['text']) < 0) {
                        $shippingInfoText = $shippingInfo['text'];
                    }
                } else {
                    if ($shippingInfo['type'] > $shippingInfoType) {
                        $shippingInfoType = $shippingInfo['type'];
                        $shippingInfoText = $shippingInfo['text'];
                    }
                }
            }
        }

        return $this->resultJsonFactory->create()->setData($shippingInfoText);
    }
}
