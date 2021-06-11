<?php

namespace Crealevant\Relevant\Cron;

class AddSpecialProduct
{

    protected $productRepository;
    protected $scopeConfig;
    protected $productCollection;
    protected $categoryLinkManagement;
    protected $bundleSelection;
    protected $logger;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
        \Magento\Bundle\Model\Product\Type $bundleSelection,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->productCollection = $productCollection;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->bundleSelection = $bundleSelection;
        $this->logger = $logger;
    }

    public function execute()
    {
        $this->logger->info("------------------------------" . date("Y-m-d H:i:s") . "----------------------------");
        $this->logger->info("----------------------- START ADD SPECIAL PRODUCT -------------------------------");

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $categoryId = $this->scopeConfig->getValue("settings/category_special_product/category", $storeScope);
        if (!$categoryId) {
            return;
        }
        $todayDate = date('Y-m-d');

        $collection = $this->productCollection->create()
            ->addAttributeToFilter(
                'special_from_date',
                [
                    ['date' => true, 'to' => $todayDate],
                ]
        );

        foreach ($collection as $row) {
            $product = $this->productRepository->create()->load($row->getEntityId());
            if ($product->getData('special_to_date') && $product->getData('special_to_date') < $todayDate) {
                continue;
            }
            $parentProduct = $this->bundleSelection->getParentIdsByChild($product->getId());
            if ($parentProduct) {
                continue;
            }
            $categories = $product->getCategoryIds();
            if ($this->isHaveCategoryId($categories, $categoryId)) {
                continue;
            }
            $categories[] = $categoryId;

            $this->categoryLinkManagement->assignProductToCategories(
                $product->getSku(),
                $categories
            );
        }

        $this->logger->info("----------------------- END ADD SPECIAL PRODUCT -------------------------------");

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
