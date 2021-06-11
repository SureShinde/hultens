<?php

namespace Crealevant\ProductRemoveCategory\Observer;

use Magento\Framework\Event\ObserverInterface;

class OutOfStock implements ObserverInterface
{
    protected $_categoryLinkRepositoryInterface;

    public function __construct(
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface $categoryLinkRepositoryInterface
    ) {
        $this->_categoryLinkRepositoryInterface = $categoryLinkRepositoryInterface;
    }

    /*
     * Check if product status is out of stock then remove all categories from it
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $product = $observer->getProduct();
            if ($product->getExtensionAttributes() && $product->getExtensionAttributes()->getStockItem() && $product->getExtensionAttributes()->getStockItem()->getIsInStock() == false) {
                $categories = $product->getCategoryIds();
                if (count($categories) > 0) {
                    foreach ($categories as $category) {
                        $this->_categoryLinkRepositoryInterface->deleteByIds($category, $product->getSku());
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}