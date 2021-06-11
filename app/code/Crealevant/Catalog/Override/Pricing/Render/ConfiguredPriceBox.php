<?php

namespace Crealevant\Catalog\Override\Pricing\Render;

use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Pricing\Price\ConfiguredPriceSelection;
use Magento\Catalog\Pricing\Render\ConfiguredPriceBox as ConfiguredPriceBoxRenderer;
use Crealevant\FeedManager\Model\Generate;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Amount\AmountFactory;

/**
 * Class for configured_price rendering.
 */
class ConfiguredPriceBox extends ConfiguredPriceBoxRenderer
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
     * @var DataObjectFactory
     */
    protected $objectFactory;

    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param array $data
     * @param SalableResolverInterface|null $salableResolver
     * @param MinimalPriceCalculatorInterface|null $minimalPriceCalculator
     * @param ConfiguredPriceSelection|null $configuredPriceSelection
     * @param Generate $generator
     * @param StoreManagerInterface $storeManager
     * @param DataObjectFactory $objectFactory
     * @param AmountFactory $amountFactory
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null,
        ConfiguredPriceSelection $configuredPriceSelection = null,
        Generate $generator,
        StoreManagerInterface $storeManager,
        DataObjectFactory $objectFactory,
        AmountFactory $amountFactory
    ) {
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $data,
            $salableResolver,
            $minimalPriceCalculator,
            $configuredPriceSelection
        );
        $this->generator = $generator;
        $this->storeManager = $storeManager;
        $this->objectFactory = $objectFactory;
        $this->amountFactory = $amountFactory;
    }

    /**
     * @param $product
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfigurableProductBasePrice($product)
    {
        $price = $this->generator->getFinalPrice($product, $this->storeManager->getStore());

        return $this->amountFactory->create($price, []);
    }
}