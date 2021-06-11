<?php

namespace Crealevant\Bundle\Helper\Catalog\Product;

class Configuration extends \Magento\Bundle\Helper\Catalog\Product\Configuration
{
    protected $pricingHelper;

    /**
     * Catalog product configuration
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $productConfiguration;

    /**
     * Escaper
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Serializer interface instance.
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $imageHelperFactory;

    protected $appEmulation;

    protected $productRepository;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfiguration,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->storeManager = $storeManager;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->appEmulation = $appEmulation;
        $this->productRepository = $productRepository;
        parent::__construct($context, $productConfiguration, $pricingHelper, $escaper, $serializer);
    }

    public function getBundleOptions(\Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item)
    {
        $options = [];
        $product = $item->getProduct();
        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $product->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption
            ? $this->serializer->unserialize($optionsQuoteItemOption->getValue())
            : [];

        if ($bundleOptionsIds) {
            /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = $this->serializer->unserialize($selectionsQuoteItemOption->getValue());

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds($bundleSelectionIds, $product);

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = ['label' => $bundleOption->getTitle(), 'value' => []];

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;
                            if ($qty) {
                                $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);

                                $option['thumbnail'] = $this->imageHelperFactory->create()
                                    ->init($bundleSelection, 'product_thumbnail_image')->getUrl();

                                $this->appEmulation->stopEnvironmentEmulation();

                                $childProduct = $this->productRepository->getById($bundleSelection->getProductId());

                                $option['delivery_info'] = $this->getTextShippingInfo($childProduct);

                                $option['value'][] = $qty . ' x '
                                    . $this->escaper->escapeHtml($bundleSelection->getName())
                                    . ' '
                                    . $this->pricingHelper->currency(
                                        $this->getSelectionFinalPrice($item, $bundleSelection)
                                    );
                                $option['has_html'] = true;
                            }
                        }

                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
        }

        return $options;
    }

    private function getTextShippingInfo($product)
    {
        $textShippingInfo = '';

        //Values
        $green = $product->getData('inventory_green_status_min'); //returns null
        $red = $product->getData('inventory_red_status_max'); // Returns string(2) "10"

        //Labels
        $greenLabel = $product->getAttributeText('inventory_green_status_text'); // Returns bool(false)
        $redLabel = $product->getAttributeText('inventory_red_status_text'); // Returns bool(false)
        $yellowLabel = $product->getAttributeText('inventory_yellow_status_text'); // Returns bool(false)

        //Stock
        $greenStat = intval($product->getData('inventory_green_status_min')); // Returns int(0)
        $redStat = intval($product->getData('inventory_red_status_max')); // Returns int(10)
        $yellowTxt = $product->getResource()->getAttribute('inventory_yellow_status_text')->getFrontend()->getValue($product); // Returns: string(46) "Fåtal kvar i lager. Leverans 2-7 arbetsdagar."
        $redTxt = $product->getResource()->getAttribute('inventory_red_status_text')->getFrontend()->getValue($product); // Returns: string(25) "Leverans 3-10 arbetsdagar"
        $greenTxt = $product->getResource()->getAttribute('inventory_green_status_text')->getFrontend()->getValue($product); // Returns: string(25) "Leverans 3-10 arbetsdagar"

        // Stock qty
        $qtyStock = $product->getExtensionAttributes()->getStockItem()->getQty();

        // Date intervall
        $TheDate = date('Y-m-d');

        $RedExtraStatusStart = $product->getData('red_xtra_status_start'); // Gets The correct date value.
        $RedExtraStatusEnd = $product->getData('red_xtra_status_end'); // Gets The correct date value.

        $RedExtraTxt = $product->getResource()->getAttribute('red_xtra_status_text')->getFrontend()->getValue($product); // Attribute: Meddelande om lång leveranstid

        if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
            $textShippingInfo = '';
        } elseif ($TheDate > $RedExtraStatusStart && $TheDate < $RedExtraStatusEnd) {
            $textShippingInfo = '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $RedExtraTxt;
        } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
            $textShippingInfo = '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $RedExtraTxt;
        } else {
            $textShippingInfo = '';
        }

        // All the above variables returns the correct data, dates and label.
        if (($TheDate > $RedExtraStatusStart && $TheDate < $RedExtraStatusEnd) || empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
            $textShippingInfo = '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $RedExtraTxt;
        }

        if ($product->getData('notforsale') && $product->isAvailable()) {
            if ($qtyStock >= $greenStat) {
                // Remove Delivery Text Depending on Setting for Red Extra Status Start And Red Extra Status End
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = '<span style="display: inline-block; background: green; border: 1px solid green; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $greenTxt;
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = '<span style="display: inline-block; background: green; border: 1px solid green; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $greenTxt;
                } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                    $textShippingInfo = '';
                    $iconShippingInfo = '';
                } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                    $textShippingInfo = '';
                    $iconShippingInfo = '';
                } else {
                    $textShippingInfo = '<span style="display: inline-block; background: green; border: 1px solid green; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $greenTxt;
                }
            } elseif ($qtyStock <= $redStat) {
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $redTxt;
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $redTxt;
                } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                    $textShippingInfo = '';
                    $iconShippingInfo = '';
                } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                    $textShippingInfo = '';
                    $iconShippingInfo = '';
                } else {
                    $textShippingInfo = '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $redTxt;
                }
            } else {
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = '<span style="display: inline-block; background: yellow; border: 1px solid yellow; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $yellowTxt;
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = '<span style="display: inline-block; background: yellow; border: 1px solid yellow; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $yellowTxt;
                } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                    $textShippingInfo = '';
                    $iconShippingInfo = '';
                } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                    $textShippingInfo = '';
                    $iconShippingInfo = '';
                } else {
                    $textShippingInfo = '<span style="display: inline-block; background: yellow; border: 1px solid yellow; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' .  $yellowTxt;
                }
            }
        }

        return $textShippingInfo;
    }
}