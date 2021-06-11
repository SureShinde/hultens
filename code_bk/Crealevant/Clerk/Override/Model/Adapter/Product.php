<?php

namespace Crealevant\Clerk\Override\Model\Adapter;

use Clerk\Clerk\Controller\Logger\ClerkLogger;
use Clerk\Clerk\Helper\Image;
use Clerk\Clerk\Model\Adapter\Product as ParentClass;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Catalog\Model\ProductFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Crealevant\Hultens\Helper\Data as HultensHelper;
use Magento\Bundle\Model\Product\Type as Bundle;

class Product extends ParentClass
{
    protected $productFactory;
    protected $hultensHelper;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $eventManager,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Image $imageHelper,
        ClerkLogger $Clerklogger,
        Stock $stockFilter,
        ProductFactory $productFactory,
        HultensHelper $hultensHelper
    ) {
        $this->productFactory = $productFactory;
        $this->hultensHelper = $hultensHelper;

        parent::__construct($scopeConfig, $eventManager, $collectionFactory, $storeManager,
            $imageHelper, $Clerklogger, $stockFilter);
    }

    protected function addFieldHandlers()
    {
        try {
            //Add price fieldhandler
            $this->addFieldHandler('price', function ($item) {
                try {
                    if ($item->getTypeId() === Bundle::TYPE_CODE) {
                        //Get minimum price for bundle products
                        $price = $this->getBundlePrice($item->getId());
                    } else {
                        $price = $item->getFinalPrice();
                    }

                    return (float)$price;
                } catch (\Exception $e) {
                    return 0;
                }
            });

            //Add list_price fieldhandler
            $this->addFieldHandler('list_price', function ($item) {
                try {
                    $price = $item->getPrice();

                    //Fix for configurable products
                    if ($item->getTypeId() === Configurable::TYPE_CODE) {
                        $price = $item->getPriceInfo()->getPrice('regular_price')->getValue();
                    }

                    if ($item->getTypeId() === Bundle::TYPE_CODE) {
                        $price = $this->getBundlePrice($item->getId());
                    }

                    return (float)$price;
                } catch (\Exception $e) {
                    return 0;
                }
            });

            //Add image fieldhandler
            $this->addFieldHandler('image', function ($item) {
                $imageUrl = $this->imageHelper->getUrl($item);

                return $imageUrl;
            });

            //Add url fieldhandler
            $this->addFieldHandler('url', function ($item) {
                return $item->getUrlModel()->getUrl($item);
            });

            //Add categories fieldhandler
            $this->addFieldHandler('categories', function ($item) {
                return $item->getCategoryIds();
            });

            //Add age fieldhandler
            $this->addFieldHandler('age', function ($item) {
                $createdAt = strtotime($item->getCreatedAt());
                $now = time();
                $diff = $now - $createdAt;
                return floor($diff / (60 * 60 * 24));
            });

            //Add on_sale fieldhandler
            $this->addFieldHandler('on_sale', function ($item) {
                try {
                    $finalPrice = $item->getFinalPrice();
                    $price = $item->getPrice();

                    return $finalPrice < $price;
                } catch (\Exception $e) {
                    return false;
                }
            });

        } catch (\Exception $e) {
            $this->clerk_logger->error('Getting Field Handlers Error', ['error' => $e->getMessage()]);
        }
    }

    private function getBundlePrice($productId)
    {
        $product = $this->productFactory->create()->load($productId);
        $selections = $product->getTypeInstance(true)
            ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            );

        $total_price_sum = 0;
        $total_price_sum_special = 0;
        $bundle = false;
        $specialprice = false;
        $isAppyRules = false;
        $total_old_prices = 0;

        foreach ($selections as $selection) {
            if($selection->getIsDefault() == '1') {
                $isSpecialprice = false;
                $qty = $selection->getSelectionQty();
                $special = $selection->getSpecialPrice();

                //Setup current Date and Get From/To Date ( Bundle/SpecialPrice )
                $todaysDate =  date("Y-m-d");
                $FromDate = $selection->getSpecialFromDate();
                $ToDate = $selection->getSpecialToDate();


                $ordprices = 0;
                $selectionProduct = $this->hultensHelper->getProduct($selection->getProductId());
                if (!$orgprices = $this->hultensHelper->getPrice($selectionProduct, $selection->getPrice())) {
                    $orgprices = $selection->getPrice();
                }
                else {
                    $isAppyRules = true;
                }
                $specialprices = 0;
                if($special){
                    $specialprices = 0;
                    if(($todaysDate > $FromDate) && ($todaysDate < $ToDate)){
                        if ($special >= $orgprices) {
                            $special = $orgprices;
                        }
                        $specialprices = $special * $qty;
                        $specialprice = true;
                        $isSpecialprice = true;
                    }
                }
                if(!$isSpecialprice){
                    if (!$ordprices=$this->hultensHelper->getPrice($selectionProduct, $selection->getPrice())) {
                        $ordprices = $selection->getPrice();
                    }
                    $ordprices *= $qty;
                }
                $orgprices *= $qty;

                $total_price_sum = $total_price_sum + $orgprices;
                $total_price_sum_special = $total_price_sum_special + $ordprices + $specialprices;
                $total_old_prices += $qty*$selection->getPrice();
            }

            $bundle = true;
        }

        if ($specialprice) {
            return $total_price_sum_special;
        }

        return $total_price_sum;
    }
}