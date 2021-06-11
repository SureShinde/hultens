<?php

namespace Crealevant\Catalog\Override\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Url\Helper\Data;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Output;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Bundle\Model\Product\Type;
use Magento\Framework\App\ObjectManager;

/**
 * Class ListProduct
 * @package Crealevant\Catalog\Override\Block\Product
 */
class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Output
     */
    protected $outputHelper;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var Type
     */
    protected $bundleProductType;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * ListProduct constructor.
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param OptionFactory $optionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Image $imageHelper
     * @param PricingHelper $pricingHelper
     * @param Type $bundleProductType
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        OptionFactory $optionFactory,
        ProductRepositoryInterface $productRepository,
        Image $imageHelper,
        PricingHelper $pricingHelper,
        Type $bundleProductType,
        array $data = []
    ) {
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
        $this->optionFactory = $optionFactory;
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        $this->pricingHelper = $pricingHelper;
        $this->bundleProductType = $bundleProductType;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getCustomizableOptions($product)
    {
        return $this->optionFactory->create()->getProductOptionCollection($product);
    }

    /**
     * @param $sku
     * @return string|ProductInterface
     */
    public function getProductBySku($sku)
    {
        $product = '';

        try {
            if ($sku) {
                $product = $this->productRepository->get($sku);
            }
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e);
        }

        return $product;
    }

    /**
     * @return Image
     */
    public function getImageHelper(): Image
    {
        return $this->imageHelper;
    }

    /**
     * @return PricingHelper
     */
    public function getPricingHelper(): PricingHelper
    {
        return $this->pricingHelper;
    }

    /**
     * @return Type
     */
    public function getBundleProductType(): Type
    {
        return $this->bundleProductType;
    }
}
