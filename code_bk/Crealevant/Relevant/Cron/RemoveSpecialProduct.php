<?php

namespace Crealevant\Relevant\Cron;

class RemoveSpecialProduct
{

    protected $productRepository;
    protected $scopeConfig;
    protected $productCollection;
    protected $categoryLink;
    protected $logger;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Model\CategoryLinkRepository $categoryLink,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->productCollection = $productCollection;
        $this->categoryLink = $categoryLink;
        $this->logger = $logger;
    }

    public function execute()
    {
        $this->logger->info("------------------------------" . date("Y-m-d H:i:s") . "----------------------------");
        $this->logger->info("----------------------- START REMOVE SPECIAL PRODUCT OUT OF CATEGORY -------------------------------");

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $categoryId = $this->scopeConfig->getValue("settings/category_special_product/category", $storeScope);
        if (!$categoryId) {
            return;
        }
        $todayDate = date('Y-m-d');

        $collection = $this->productCollection->create()
            ->addAttributeToFilter(
                [
                    [
                        'attribute' => 'special_from_date',
                        'gt' => $todayDate,
                    ],
                    [
                        'attribute' => 'special_to_date',
                        'lt' => $todayDate,
                    ],
                ]
            );
        foreach ($collection as $row) {
            $product = $this->productRepository->create()->load($row->getEntityId());
            $categories = $product->getCategoryIds();
            if ($this->isHaveCategoryId($categories, $categoryId)) {
                $this->categoryLink->deleteByIds($categoryId, $product->getSku());
            }
        }

        $this->logger->info("----------------------- END REMOVE SPECIAL PRODUCT OUT OF CATEGORY -------------------------------");

        return $this;
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
