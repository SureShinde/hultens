<?php

namespace Crealevant\Hultens\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $ruleFactory;
    protected $productFactory;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory
    ) {
        $this->productFactory = $productFactory;
        $this->ruleFactory = $ruleFactory;
    }

    public function getProduct($productId)
    {
        $product = $this->productFactory->create()->load($productId);
        return $product;
    }

    public function getPrice($product, $orgprices) {
        $rule = $this->ruleFactory->create();
        return $rule->calcProductPriceRule($product,$orgprices);
    }
}

