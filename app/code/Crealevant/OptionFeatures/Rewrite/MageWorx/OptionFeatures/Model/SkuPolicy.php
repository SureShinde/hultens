<?php

namespace Crealevant\OptionFeatures\Rewrite\MageWorx\OptionFeatures\Model;

use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\OptionSkuPolicy\Helper\Data as Helper;

class SkuPolicy extends \MageWorx\OptionSkuPolicy\Model\SkuPolicy
{
    /**
     * Process selectable option
     *
     * @param ProductOptionInterface $option
     * @param int $optionId
     * @param array $values
     * @return void
     */
    protected function processSelectableOption($option, $optionId, $values)
    {
        if (is_array($values)) {
            $optionTypeIds = $values;
        } else {
            $optionTypeIds = explode(',', $values);
        }
        $isOneTime = $option->getOneTime();
        $skuPolicy = $option->getSkuPolicy() == Helper::SKU_POLICY_USE_CONFIG
            ? $this->productSkuPolicy
            : $option->getSkuPolicy();

        $replacementSkus = [];
        foreach ($optionTypeIds as $index => $optionTypeId) {
            if (!$optionTypeId) {
                continue;
            }
            $value = $option->getValueById($optionTypeId);
            $sku   = $value->getSku();
            if (!$sku) {
                continue;
            }
            $replacementSkus[] = $sku;

            if ($skuPolicy == Helper::SKU_POLICY_STANDARD) {
                $this->skuArray[] = $sku;
            } elseif ($skuPolicy == Helper::SKU_POLICY_REPLACEMENT) {
                $this->skuArray[0] = implode('-', $replacementSkus);
            } elseif ($skuPolicy == Helper::SKU_POLICY_GROUPED
                || $skuPolicy == Helper::SKU_POLICY_INDEPENDENT
            ) {
                try {
                    $excludedItemCandidate = $this->productRepository->get($sku);
                } catch (NoSuchEntityException $e) {
                    $this->skuArray[] = $sku;
                    continue;
                }
                if (!$this->isExcludedItemValid($excludedItemCandidate)) {
                    $this->skuArray[] = $sku;
                    continue;
                }

                $optionQty      = $this->getOptionQty($this->buyRequest, $optionId, $optionTypeId);
                $optionTotalQty = $isOneTime ? $optionQty : $optionQty * $this->quoteItem->getQty();

                $request = $this->dataObjectFactory->create();
                $request->setQty($optionTotalQty);

                $excludedProduct = $this->productRepository->get($sku, false, $this->quoteItem->getStoreId(), true);
                if ($this->helper->isSplitIndependents()) {
                    $excludedProduct->addCustomOption('parent_custom_option_id', $option->getOptionId());
                }

                $excludedItem = $this->quoteItem->getQuote()->addProduct(
                    $excludedProduct,
                    $request
                );
                if (!is_object($excludedItem)) {
                    continue;
                }
                $this->quoteItem->getQuote()->setIsSuperMode(true);
                /**
                 * start override
                 * Fix: Catalog Price Rule Not Working On Custom Options
                 */
                $price = (float)$excludedProduct->getFinalPrice();
                /** End override */
                $price = $this->priceCurrency->convert(
                    $price,
                    $this->quoteItem->getQuote()->getStore()
                );

                $excludedItem->setOriginalCustomPrice($price);
                $excludedItem->setCustomPrice($price);

                if ($this->helperFeatures->isWeightEnabled()) {
                    $excludedItem->setWeight($value->getWeight());
                }
                if ($this->helperFeatures->isCostEnabled()) {
                    $excludedItem->setCost($value->getCost());
                }
                $excludedItem->setIsSkuPolicyApplied(true);
                if (!in_array($excludedItem, $this->newQuoteItems, true)) {
                    $this->newQuoteItems[] = $excludedItem;
                }

                $this->removeOptionAndOptionValueFromItem(
                    $values,
                    $optionId,
                    $index
                );

                if (!$this->toCart) {
                    $this->removeOutdatedQuoteItemData();
                }

                $this->isItemChanged = true;
                if ($skuPolicy == Helper::SKU_POLICY_GROUPED && $this->isGroupedSkuPolicyOnly) {
                    $this->isItemRemoved = true;
                }
            }
        }
    }
}