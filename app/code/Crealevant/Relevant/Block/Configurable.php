<?php
/**
 * Catalog super product configurable part block
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Crealevant\Relevant\Block;

use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Eav\Model\AttributeRepository;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Swatches\Helper\Data as SwatchData;
use Magento\Swatches\Helper\Media;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    /**
     * Composes configuration for js
     *
     * @return string
     */
    protected $attributeRepository;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        AttributeRepository $attributeRepository,
        SwatchData $swatchHelper,
        Media $swatchMediaHelper,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
        $this->catalogProduct = $catalogProduct;
        $this->currentCustomer = $currentCustomer;
        $this->configurableAttributeData = $configurableAttributeData;
        $this->attributeRepository = $attributeRepository;
        $this->swatchHelper = $swatchHelper;
        $this->swatchMediaHelper = $swatchMediaHelper;
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $helper,
            $catalogProduct,
            $currentCustomer,
            $priceCurrency,
            $configurableAttributeData,
            //$attributeRepository,
            $swatchHelper,
            $swatchMediaHelper,
            $data
        );
    }

    public function getJsonConfig()
    {
        $store = $this->getCurrentStore();
        $currentProduct = $this->getProduct();

        $regularPrice = $currentProduct->getPriceInfo()->getPrice('regular_price');
        $finalPrice = $currentProduct->getPriceInfo()->getPrice('final_price');

        $options = $this->helper->getOptions($currentProduct, $this->getAllowProducts());
        $attributesData = $this->configurableAttributeData->getAttributesData($currentProduct, $options);

        $config = [
            'attributes' => $attributesData['attributes'],
            'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'optionPrices' => $this->getOptionPrices(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->_registerJsPrice($regularPrice->getAmount()->getValue()),
                ],
                'basePrice' => [
                    'amount' => $this->_registerJsPrice(
                        $finalPrice->getAmount()->getBaseAmount()
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->_registerJsPrice($finalPrice->getAmount()->getValue()),
                ],
            ],
            'shoeSizeaj' => $this->getAttrProducts(),
            'productId' => $currentProduct->getId(),
            'chooseText' => __('Choose an Option...'),
            'images' => isset($options['images']) ? $options['images'] : [],
            'index' => isset($options['index']) ? $options['index'] : [],
        ];

        if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $config['defaultValues'] = $attributesData['defaultValues'];
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return $this->jsonEncoder->encode($config);
    }

    protected function getAttrProducts()
    {
        $stockStatus = [];
        $currentProduct = $this->getProduct();
        $options = $this->helper->getOptions($currentProduct, $this->getAllowProducts());
        /** @var \Magento\Eav\Api\Data\AttributeInterface $stockAttr */
        $attributesData = $this->configurableAttributeData->getAttributesData($currentProduct, $options);

        $attrOptionsBase = $attributesData['attributes'];

        foreach ($attrOptionsBase as $i => $product) {
            $attrOptions = $attributesData['attributes'][$i]['options'];
            foreach ($attrOptions as $i => $product) {
                $simpleProductId = $attrOptions[$i]['products'][0];
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
                $getProduct = $productRepository->getById($simpleProductId);
                $attrtext = $getProduct->getData('quantity_and_stock_status');
                $attrtext = $attrtext['is_in_stock'];
                $stockStatus[$simpleProductId] =
                    [
                        'stockstatus' => $attrtext
                    ];
            }
        }

        return $stockStatus;
    }

}