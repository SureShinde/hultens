<?php

namespace Crealevant\FeedManager\Model;

use Magento\Framework\App\Language\Dictionary;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use mysql_xdevapi\Exception;
use Magento\Catalog\Model\Product\Visibility;

class Generate extends \Magento\Framework\Model\AbstractModel
{
    /** @var \Magento\Framework\App\Filesystem\DirectoryList $_directoryList */
    private $_directoryList;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_productCollectionFactory */
    private $_productCollectionFactory;

    /** @var int $_attributeSetId */
    private $_attributeSetId;

    /** @var array $_fieldAttributes */
    private $_fieldAttributes;

    /** @var \Magento\Store\Model\StoreManager $_storeManager */
    private $_storeManager;

    /** @var \Magento\Catalog\Model\CategoryFactory $_categoryFactory */
    private $_categoryFactory;

    /** @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $_categoryCollectionFactory */
    private $_categoryCollectionFactory;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig */
    private $_scopeConfig;

    /** @var \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository */
    private $_stockItemRepository;

    /** @var \Crealevant\FeedManager\Model\ResourceModel\FeedAttribute\CollectionFactory $_feedAttributeCollection */
    protected $_feedAttributeCollection;

    /** @var \Crealevant\FeedManager\Model\ResourceModel\Feeds\CollectionFactory */
    protected $_feedCollection;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    protected $dictionary;

    protected $emulation;

    protected $option;

    /**
     * Generate constructor.
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param ResourceModel\FeedAttribute\CollectionFactory $_feedAttributeCollection
     * @param ResourceModel\Feeds\CollectionFactory $_feedCollection
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Crealevant\FeedManager\Model\ResourceModel\FeedAttribute\CollectionFactory $_feedAttributeCollection,
        \Crealevant\FeedManager\Model\ResourceModel\Feeds\CollectionFactory $_feedCollection,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        StoreRepositoryInterface $storeRepository,
        Dictionary $dictionary,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Bundle\Model\Option $option,
        \Crealevant\Hultens\Helper\Data $hultensHelper
    ) {
        $this->_directoryList = $directoryList;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_stockItemRepository = $stockItemRepository;
        $this->stockRegistry = $stockRegistry;
        $this->_feedAttributeCollection = $_feedAttributeCollection;
        $this->_feedCollection = $_feedCollection;
        $this->storeRepository = $storeRepository;
        $this->dictionary = $dictionary;
        $this->emulation = $emulation;
        $this->option = $option;
        $this->hultensHelper = $hultensHelper;

        $attributeSets = $attributeSetCollection->create()->addFieldToSelect('*')
            ->addFieldToFilter('attribute_set_name', 'default');
        $this->_attributeSetId = 0;
        foreach ($attributeSets as $attributeSet) {
            $this->_attributeSetId = $attributeSet->getAttributeSetId();
        }
    }

    public function execute()
    {
        $_feedCollection = $this->_feedCollection->create()->addFieldToFilter('status', ['eq' => 1]);

        foreach ($_feedCollection as $feed) {
            $fieldNames = null;
            $collectionFeedAttribute = $this->_feedAttributeCollection->create()->addFeedIdFilter($feed->getId());
            foreach ($collectionFeedAttribute as $feedAttribute) {
                $fieldNames[] = $feedAttribute->getTitle();
            }

            //$file = $this->_directoryList->getPath('pub') . "/productexport/product-feed-{$feed->getId()}.csv";
            $file = $feed->getPath();
            switch ($feed->getData('file_type')) {
                case 0:
                    // put TXT
                    $this->putTXT($file, $fieldNames, $feed);
                    break;

                case 2:
                    // put XML
                    $this->putXML($file, $feed);
                    break;

                default:
                    // put CSV
                    $this->putCsv($file, $fieldNames, $feed);
            }
        }


    }

    protected function putCsv($file, $fieldNames, $feed)
    {
        if (file_exists($file)) {
            unlink($file);
        }
        $file = fopen($file, 'x');
        fputcsv($file, $fieldNames, ';');

        $this->_exportData($file, $feed, 'csv');

        fclose($file);
    }

    protected function putTXT($file, $fieldNames, $feed)
    {
        if (file_exists($file)) {
            unlink($file);
        }
        $file = fopen($file, "x");
        //draw title
        foreach ($fieldNames as $title) {
            fwrite($file, $title . "\t\t");
        }

        fwrite($file, "\n");

        //draw feed
        $this->_exportData($file, $feed, 'txt');

        fclose($file);
    }

    protected function putXML($file, $feed)
    {
        try {
            if (file_exists($file)) {
                unlink($file);
            }
            $this->_exportData($file, $feed, 'xml');
        } catch (\Exception $ex) {
            echo $ex->getMessage() . "\n";
            var_dump($ex->getTraceAsString());
        }
    }


    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param resource $file
     * @throws \Exception
     */
    private function _exportData($file, $feed, $type = 'csv')
    {
        $feedId = $feed->getId();
        $store = $this->storeRepository->getById($feed->getStoreId());

        $this->emulation->startEnvironmentEmulation($store->getId(), \Magento\Framework\App\Area::AREA_FRONTEND, true);

        if (!$store) {
            throw new \Exception('The store that was requested wasn\'t found. Verify the store and try again.');
        }

        $this->_fieldAttributes = null;
        $collectionFeedAttribute = $this->_feedAttributeCollection->create()->addFeedIdFilter($feedId);

        foreach ($collectionFeedAttribute as $feedAttribute) {
            $this->_fieldAttributes[] = [
                'title' => $feedAttribute->getTitle(),
                'value' => $feedAttribute->getAttributeValue()
            ];
        }

        if ($type == 'xml') {
            $shop_name = $feed->getName();
            $shop_link = $this->_storeManager->getStore()->getBaseUrl();
            $doc = new \DOMDocument('1.0', 'UTF-8');
            $xmlRoot = $doc->createElement("rss");
            $xmlRoot = $doc->appendChild($xmlRoot);

            $xmlRoot->setAttribute('version', '2.0');
            $xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:g', "http://base.google.com/ns/1.0");

            $channelNode = $xmlRoot->appendChild($doc->createElement('channel'));
            $channelNode->appendChild($doc->createElement('title', $shop_name));
            $channelNode->appendChild($doc->createElement('link', $shop_link));
        }


        $arrFieldAttributes = [];
        foreach ($this->_fieldAttributes as $attribute) {
            $arrFieldAttributes[] = $attribute['value'];
        }

        $excludeOutStock = $feed->getExcludeOutStock();
        $excludeConfigBundle = $feed->getExcludeConfigBundle();
        if (!$feed->getExportCategory() && in_array('category_ids', $arrFieldAttributes)) {
            $_categories = $this->getCategoriesByIncludeFeed();

            foreach ($_categories as $category) {
                $productsCategory = $category->getProductCollection()->addAttributeToSelect('*')->addStoreFilter($store)
                    ->setVisibility($this->getVisibleInSiteIds());

                foreach ($productsCategory as $product) {
                    if ($excludeOutStock) {
                        $productStock = $this->stockRegistry->getStockItem($product->getId());
                        if (empty($productStock->getIsInStock())) {
                            continue;
                        }
                    }

                    if ($excludeConfigBundle) {
                        $typeId = $product->getTypeId();
                        if ($typeId == "configurable" || $typeId == "bundle") {
                            continue;
                        }
                    }

                    if ($type == 'csv') {
                        //CSV
                        fputcsv($file, array_values($this->getProductData($product, $store, $type)), ';');
                    } elseif ($type == 'txt') {
                        //TXT
                        foreach (array_values($this->getProductData($product, $store, $type)) as $value) {
                            fwrite($file, $value . "\t\t");
                        }
                        fwrite($file, "\n");
                    } elseif ($type == 'xml') {
                        echo "{$product->getId()}\n";
                        //XML
                        $itemNode = $channelNode->appendChild($doc->createElement('item'));
                        foreach ($this->getProductData($product, $store, $type) as $key => $value) {
                            foreach ($this->_fieldAttributes as $attribute) {
                                if (trim($key) == trim($attribute['value'])) {
                                    $key = 'g:' . trim($attribute['title']);
                                    break;
                                }
                            }

                            if ($key == "g:description") {
                                $itemNode->appendChild($doc->createElement(trim($key)))->appendChild($doc->createCDATASection(trim($value)));
                            } else {
                                $itemNode->appendChild($doc->createElement(trim($key)))->appendChild($doc->createTextNode(trim($value)));
                            }
                        }
                    }
                }
            }
            if ($type == 'xml') {
                $doc->formatOutput = true;
                $doc->saveXML();
                $doc->save($file);
            }
        } else {
            if (!$excludeConfigBundle) {
                $products = $this->_productCollectionFactory->create()->addStoreFilter($store)
                    ->addFieldToFilter('attribute_set_id', $this->_attributeSetId)
                    ->addFieldToFilter('type_id', 'configurable')
                    ->setVisibility($this->getVisibleInSiteIds())
                    ->getItems();

                /** @var \Magento\Catalog\Model\Product $product */
                foreach ($products as $product) {
                    if ($excludeOutStock) {
                        $productStock = $this->stockRegistry->getStockItem($product->getId());
                        if (empty($productStock->getIsInStock())) {
                            continue;
                        }
                    }

                    if ($type == 'csv') {
                        //CSV
                        fputcsv($file, array_values($this->getProductData($product, $store, $type)), ';');
                    } elseif ($type == 'txt') {
                        //TXT
                        foreach (array_values($this->getProductData($product, $store, $type)) as $value) {
                            fwrite($file, $value . "\t\t");
                        }
                        fwrite($file, "\n");
                    } else {
                        //XML
                        $itemNode = $channelNode->appendChild($doc->createElement('item'));
                        foreach ($this->getProductData($product, $store, 'xml') as $key => $value) {
                            foreach ($this->_fieldAttributes as $attribute) {
                                if (trim($key) == trim($attribute['value'])) {
                                    $key = 'g:' . trim($attribute['title']);
                                    break;
                                }
                            }

                            if ($key == "g:description") {
                                $itemNode->appendChild($doc->createElement(trim($key)))->appendChild($doc->createCDATASection(trim($value)));
                            } else {
                                $itemNode->appendChild($doc->createElement(trim($key)))->appendChild($doc->createTextNode(trim($value)));
                            }
                        }
                    }
                }
            }

            $products = $this->_productCollectionFactory->create()->addStoreFilter($store)
                ->addFieldToFilter('attribute_set_id',
                    array('neq' => $this->_attributeSetId))
                ->setVisibility($this->getVisibleInSiteIds())
                ->getItems();

            /** @var \Magento\Catalog\Model\Product $product */
            $i = 1;
            foreach ($products as $product) {
                if ($excludeOutStock) {
                    $productStock = $this->stockRegistry->getStockItem($product->getId());
                    if (empty($productStock->getIsInStock())) {
                        continue;
                    }
                }

                if ($excludeConfigBundle) {
                    $typeId = $product->getTypeId();
                    if ($typeId == "configurable" || $typeId == "bundle") {
                        continue;
                    }
                }

                if ($type == 'csv') {
                    //CSV
                    fputcsv($file, array_values($this->getProductData($product, $store, $type)), ';');
                } elseif ($type == 'txt') {
                    //TXT
                    foreach (array_values($this->getProductData($product, $store, $type)) as $value) {
                        fwrite($file, $value . "\t\t");
                    }
                    fwrite($file, "\n");
                } else {
                    //XML
                    echo "{$product->getId()} \n";
                    $itemNode = $channelNode->appendChild($doc->createElement('item'));
                    foreach ($this->getProductData($product, $store, 'xml') as $key => $value) {
                        foreach ($this->_fieldAttributes as $attribute) {
                            if (trim($key) == trim($attribute['value'])) {
                                $key = 'g:' . trim($attribute['title']);
                                break;
                            }
                        }

                        if ($key == "g:description") {
                            $itemNode->appendChild($doc->createElement(trim($key)))->appendChild($doc->createCDATASection(trim($value)));
                        } else {
                            $itemNode->appendChild($doc->createElement(trim($key)))->appendChild($doc->createTextNode(trim($value)));
                        }
                    }
                }
                $i++;
            }
            if ($type == 'xml') {
                $doc->formatOutput = true;
                $doc->saveXML();
                $doc->save($file);
            }
        }
        $this->emulation->stopEnvironmentEmulation();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Model\Store $store
     * @return array
     */
    public function getProductData($product, $store, $exportType = "csv")
    {
        $localeCode = $this->_scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE,
            $store->getId());

        $csv = [];
        $product = $product->load($product->getId());

        foreach ($this->_fieldAttributes as $attribute) {
            $attribute = $attribute['value'];
            $csv[$attribute] = "";
            // These are attributes that aren't part of the product object
            // and must be fetched in other ways.
            if (in_array($attribute, [
                "shipping",
                "currency",
                "quantity_and_stock_status",
                "shipping_fee",
                "shipping_currency",
                "status",
                "category_ids",
                "image",
                "product_url",
                "small_image",
                "shoe_color",
                "shoe_size",
                "price_currency",
                "special_price_currency"
            ])) {
                if ($attribute == "quantity_and_stock_status") {
                    $_productStock = $this->stockRegistry->getStockItem($product->getId());
                    if ($_productStock->getIsInStock() == '1') {
                        if (isset($this->dictionary->getDictionary($localeCode)['In stock'])) {
                            $csv[$attribute] = $this->dictionary->getDictionary($localeCode)['In stock'];
                        } else {
                            $csv[$attribute] = "In stock";
                        }
                    } else {
                        if (isset($this->dictionary->getDictionary($localeCode)['Out of stock'])) {
                            $csv[$attribute] = $this->dictionary->getDictionary($localeCode)['Out of stock'];
                        } else {
                            $csv[$attribute] = "Out of stock";
                        }
                    }
                } else {
                    if ($attribute == "shipping_fee") {
                        $csv[$attribute] = $this->_scopeConfig->getValue('carriers/flatrate/price'); //flatrate price - shipping fee
                    } else {
                        if (in_array($attribute, ["currency", "shipping_currency"])) {
                            $csv[$attribute] = $store->getCurrentCurrencyCode();
                        } else {
                            if ($attribute == "price_currency") {
                                $csv[$attribute] = $this->getFinalPrice($product, $store) . " " . $store->getCurrentCurrencyCode();
                            } else {
                                if ($attribute == "special_price_currency") {
                                    $specialPrice = $product->getData("special_price");
                                    if (empty($specialPrice)) {
                                        $csv[$attribute] = "";
                                    } else {
                                        $csv[$attribute] = round($specialPrice) . " " . $store->getCurrentCurrencyCode();
                                    }
                                } else {
                                    if ($attribute == "category_ids") {
                                        $categories = null;
                                        $level = null;
                                        /** @var \Magento\Catalog\Model\Category $category */
                                        $_collection_categories = $product->getCategoryCollection();
                                        foreach ($_collection_categories->getItems() as $category) {
                                            if ($level == null || $level < $category->getLevel()) {
                                                $level = $category->getLevel();
                                                $categories = $category->getPath();
                                            }
                                        }

                                        if (strlen($categories)) {
                                            $categories = explode('/', $categories);
                                            foreach ($categories as $category) {
                                                /** @var \Magento\Catalog\Model\Category $category */
                                                $category = $this->_categoryFactory->create()->load($category);
                                                if (strlen($category->getName()) && $category->getId() > 1) {
                                                    if (!strlen($csv[$attribute])) {
                                                        $csv[$attribute] = $category->getName();
                                                    } else {
                                                        $csv[$attribute] .= ' > ' . $category->getName();
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if ($attribute == "image") {
                                            $csv[$attribute] =
                                                $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "catalog/product/" .
                                                $product->getImage();
                                        } else {
                                            if ($attribute == "small_image") {
                                                $csv[$attribute] =
                                                    $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "catalog/product/" .
                                                    $product->getImage();
                                            } else {
                                                if ($attribute == "product_url") {
                                                    $csv[$attribute] =
                                                        $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . $product->getUrlKey() . $this->_scopeConfig->getValue('catalog/seo/product_url_suffix');
                                                } else {
                                                    if ($attribute == "shoe_size") {
                                                        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {

                                                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                                                            $configProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getID());
                                                            $ids = '"';
                                                            $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
                                                            $firstSize = true;

                                                            foreach ($_children as $child) {

                                                                $childSize = "";
                                                                if (!empty($child->getData($attribute)) || $child->getData($attribute != "")) {
                                                                    if (!$firstSize) {
                                                                        $ids .= ',';
                                                                    }

                                                                    $childSize = $child->getAttributeText($attribute);
                                                                }

                                                                $ids .= $childSize;
                                                                $firstSize = false;
                                                            }
                                                            $ids .= '"';

                                                            $csv[$attribute] = $ids;
                                                        } else {

                                                        }
                                                    } else {
                                                        if ($attribute == "shoe_color") {
                                                            $ids = '';
                                                            if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {

                                                                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                                                                $configProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getID());
                                                                $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
                                                                $firstColor = true;

                                                                foreach ($_children as $child) {

                                                                    $childColor = "";
                                                                    // We only want one color, from the first child product
                                                                    if (($firstColor) && !empty($child->getData($attribute)) || $child->getData($attribute != "")) {
                                                                        if (!$firstColor) {
                                                                            $ids .= ',';
                                                                        }

                                                                        $childColor = $child->getAttributeText($attribute);
                                                                    }

                                                                    $ids .= $childColor;
                                                                    $firstColor = false;
                                                                }

                                                                $csv[$attribute] = $ids;
                                                            } else {

                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if ($attribute == "special_price") {
                    $csv[$attribute] = round($product->getData($attribute));
                } elseif ($attribute == "price") {
                    $csv[$attribute] = $this->getFinalPrice($product, $store);
                } else if ($attribute == "manufacturer") {
                    $csv[$attribute] = $product->getAttributeText($attribute);
                } else {
                    if (!empty($product->getData($attribute))) {
                        $value = $product->getAttributeText($attribute);
                        if (empty($value)) {
                            $value = $product->getData($attribute);
                        }
                        $csv[$attribute] = $value;
                    } else {
                        $csv[$attribute] = "";
                    }
                }
            }
        }

        return $csv;
    }

    protected function getCategoriesByIncludeFeed()
    {
        $categories = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('include_in_feed', ['eq' => 1])
            ->setStore($this->_storeManager->getStore());

        return $categories;
    }

    private function getVisibleInSiteIds()
    {
        return [Visibility::VISIBILITY_IN_SEARCH, Visibility::VISIBILITY_IN_CATALOG, Visibility::VISIBILITY_BOTH];
    }

    private function getFinalPrice($product, $store)
    {
        $price = floatval($product->getData('price'));

        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $storeId = $store->getId();
            $options = $this->option->getResourceCollection()
                ->setProductIdFilter($product->getId())
                ->setPositionOrder();
            $options->joinValues($storeId);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $typeInstance = $objectManager->get('Magento\Bundle\Model\Product\Type');
            $selections = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);

            $total_price_sum = 0;
            foreach ($selections as $selection) {
                if ($selection->getIsDefault() == '1') {
                    $specialprice = false;
                    $qty = $selection->getSelectionQty();
                    $special = $selection->getSpecialPrice();

                    //Setup current Date and Get From/To Date ( Bundle/SpecialPrice )
                    $todaysDate = date("Y-m-d");
                    $FromDate = $selection->getSpecialFromDate();
                    $ToDate = $selection->getSpecialToDate();


                    $ordprices = 0;
                    $selectionProduct = $this->hultensHelper->getProduct($selection->getProductId());

                    $orgprices = $this->hultensHelper->getPrice($selectionProduct, $selection->getPrice());
                    if (!$orgprices) {
                        $orgprices = $selection->getPrice();
                    }

                    $orgprices *= $qty;
                    $specialprices = 0;
                    if ($special) {
                        if (($todaysDate > $FromDate) && ($todaysDate < $ToDate)) {
                            $specialprices = $special * $qty;
                            $specialprice = true;
                        }
                    }

                    if ($specialprice) {
                        if ($specialprices > $orgprices)
                            $total_price_sum += $orgprices;
                        else
                            $total_price_sum += $specialprices;
                    } else {
                        $total_price_sum += $orgprices;
                    }
                }
            }

            $price = $total_price_sum;
        } elseif ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
            $currentDate = date("Y-m-d");
            $fromDate = $product->getSpecialFromDate();
            $toDate = $product->getSpecialToDate();
            $special = $product->getSpecialPrice();
            $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
            if ($special) {
                if ((empty($fromDate) && empty($toDate)) || (($currentDate > $fromDate) && ($currentDate < $toDate))) {
                    $price = $special;
                }
            }
            if ($finalPrice) {
                $price = $finalPrice;
            }
        }

        return $price;
    }
}