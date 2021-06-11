<?php

namespace Crealevant\Catalog\Override\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\View as ViewBlock;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Json\EncoderInterface as JsonEncoder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Crealevant\FeedManager\Model\Generate;

/**
 * Class View
 * @package Crealevant\Catalog\Override\Block\Product
 */
class View extends ViewBlock
{
    /**
     * @var Generate
     */
    protected $generator;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param EncoderInterface $urlEncoder
     * @param JsonEncoder $jsonEncoder
     * @param StringUtils $string
     * @param Product $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface|PriceCurrencyInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param Generate $generator
     * @param StoreManagerInterface $storeManager
     * @param array $data
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        EncoderInterface $urlEncoder,
        JsonEncoder $jsonEncoder,
        StringUtils $string,
        Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        Generate $generator,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->generator = $generator;
        $this->storeManager = $storeManager;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @override Calculate base price
     * @return string
     */
    public function getJsonConfig()
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();

        if (!$this->hasOptions()) {
            $config = [
                'productId' => $product->getId(),
                'priceFormat' => $this->_localeFormat->getPriceFormat()
            ];
            return $this->_jsonEncoder->encode($config);
        }

        $tierPrices = [];
        $tierPricesList = $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPrices[] = $tierPrice['price']->getValue();
        }

        // Start overriding
        $config = [
            'productId' => $product->getId(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->getConfigurableProductBasePrice($product),
                    'adjustments' => []
                ],
                'basePrice' => [
                    'amount' => $this->getConfigurableProductBasePrice($product),
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount' => $this->getConfigurableProductBasePrice($product),
                    'adjustments' => []
                ]
            ],
            'idSuffix' => '_clone',
            'tierPrices' => $tierPrices
        ];
        // End override

        $responseObject = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->_jsonEncoder->encode($config);
    }

    /**
     * @param $product
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function getConfigurableProductBasePrice($product)
    {
        return $this->generator->getFinalPrice($product, $this->storeManager->getStore());
    }
}