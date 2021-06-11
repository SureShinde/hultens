<?php

namespace Crealevant\Relevant\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddSpecialProductToCategory implements ObserverInterface
{
    protected $categoryLinkManagement;
    protected $categoryLink;
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
        \Magento\Catalog\Model\CategoryLinkRepository $categoryLink
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->categoryLink = $categoryLink;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $todayDate = date('Y-m-d');
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $categoryId = $this->scopeConfig->getValue("settings/category_special_product/category", $storeScope);
        if (!$categoryId) {
            return;
        }
        $categories = $product->getCategoryIds();

        if ($product->getData('special_from_date') && $product->getData('special_from_date') <= $todayDate) {
            if ($product->getData('special_to_date') && $product->getData('special_to_date') < $todayDate) {
                if ($this->isHaveCategoryId($categories, $categoryId)) {
                    $this->categoryLink->deleteByIds($categoryId, $product->getSku());
                }
            } else {
                if (!$this->isHaveCategoryId($categories, $categoryId)) {
                    $categories[] = $categoryId;
                    $this->categoryLinkManagement->assignProductToCategories(
                        $product->getSku(),
                        $categories
                    );
                }
            }
        } else {
            if ($this->isHaveCategoryId($categories, $categoryId)) {
                $this->categoryLink->deleteByIds($categoryId, $product->getSku());
            }
        }
    }

    private function isHaveCategoryId($categories, $id)
    {
        foreach ($categories as $category) {
            if ($category == $id) {
                return true;
            }
        }
        return false;
    }
}
