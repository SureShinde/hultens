<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Crealevant\MageWorxOptionSwatches\Plugin\Product\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\Type\Select as TypeSelect;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use MageWorx\OptionFeatures\Model\Price as AdvancedPricingPrice;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionBase\Helper\Price as BasePriceHelper;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;
use MageWorx\OptionBase\Helper\System as SystemHelper;
use MageWorx\OptionBase\Model\HiddenDependents as HiddenDependentsModel;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Select
{
    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var BasePriceHelper
     */
    protected $basePriceHelper;

    /**
     * @var AdvancedPricingPrice
     */
    protected $advancedPricingPrice;
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var StockRegistryInterface
     */

    protected $stockRegistry;
    /**
     * @var SystemHelper
     */
    protected $systemHelper;

    /**
     * @var HiddenDependentsModel
     */
    protected $hiddenDependentsModel;

    /**
     * @param PricingHelper $pricingHelper
     * @param Helper $helper
     * @param BaseHelper $baseHelper
     * @param BasePriceHelper $basePriceHelper
     * @param AdvancedPricingPrice $advancedPricingPrice
     * @param ProductRepository $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param State $state
     * @param SystemHelper $systemHelper
     * @param HiddenDependentsModel $hiddenDependentsModel
     */
    public function __construct(
        PricingHelper $pricingHelper,
        Helper $helper,
        BaseHelper $baseHelper,
        BasePriceHelper $basePriceHelper,
        AdvancedPricingPrice $advancedPricingPrice,
        State $state,
        SystemHelper $systemHelper,
        HiddenDependentsModel $hiddenDependentsModel,
        ProductRepository $productRepository,
        StockRegistryInterface $stockRegistry
    ) {
        $this->pricingHelper         = $pricingHelper;
        $this->helper                = $helper;
        $this->baseHelper            = $baseHelper;
        $this->basePriceHelper       = $basePriceHelper;
        $this->advancedPricingPrice  = $advancedPricingPrice;
        $this->state                 = $state;
        $this->systemHelper          = $systemHelper;
        $this->hiddenDependentsModel = $hiddenDependentsModel;
        $this->_productRepository    = $productRepository;
        $this->_stockRegistry        = $stockRegistry;
    }

    /**
     * Return html for control element
     *
     * @param TypeSelect $subject
     * @param \Closure $proceed
     * @return string
     */
    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }

    public function getStockItem($productId)
    {
        return $this->_stockRegistry->getStockItem($productId);
    }
    public function aroundGetValuesHtml(TypeSelect $subject, \Closure $proceed)
    {
        $option = $subject->getOption();
        if (($option->getType() == Option::OPTION_TYPE_DROP_DOWN ||
                $option->getType() == Option::OPTION_TYPE_MULTIPLE) &&
            $this->state->getAreaCode() !== Area::AREA_ADMINHTML &&
            $option->getIsSwatch()
        ) {
            $renderSwatchOptions       = '';
            $isHiddenOutOfStockOptions = $this->baseHelper->isHiddenOutOfStockOptions();
            /** @var ProductCustomOptionValuesInterface $value */
            foreach ($option->getValues() as $value) {
                if ($value->getManageStock() && $value->getQty() <= 0 && $isHiddenOutOfStockOptions) {
                    $renderSwatchOptions .= "";
                } else {
                    $renderSwatchOptions .= $this->getOptionSwatchHtml($option, $value);
                }
            }
            $renderSwatchSelect = $this->getOptionSwatchHiddenHtml($subject);
            $divClearfix        = '<div class="swatch-attribute-options clearfix">';
            $divStart           = '<div class="swatch-attribute size">';
            $divEnd             = '</div>';

            $selectHtml = $divStart . $divClearfix . $renderSwatchOptions . $renderSwatchSelect . $divEnd . $divEnd;

            return $selectHtml;
        }

        return $proceed();
    }
    private function getOptionPrice($optionValue)
    {
        if($optionValue->getData('sku')){

            $sku = $optionValue->getData('sku');
            $_product = $this->getProductBySku($sku);
            $finalPrice = $this->pricingHelper->currency($_product->getPriceInfo()->getPrice('final_price')->getValue(), true, false);

            return $finalPrice;
        }
    }
    private function optionisAppyRules($optionValue)
    {
        if($optionValue->getData('sku')){
            $isAppyRules = false;
            $sku = $optionValue->getData('sku');
            $_product = $this->getProductBySku($sku);
            // Data - Get Product prices.
            $catalogPriceRule = $_product->getPriceInfo()->getPrice('catalog_rule_price')->getValue();

            if($catalogPriceRule){
                $isAppyRules = true;
            }
            return $isAppyRules;
        }
    }

    private function optionIsSpecial($optionValue)
    {
        if($optionValue->getData('sku')) {
            $isSpecial = false;
            $sku = $optionValue->getData('sku');
            $_product = $this->getProductBySku($sku);

            // Data - Get Product prices.
            $_specialPrice = $this->pricingHelper->currency($_product->getSpecialPrice(), false, false);

            // Data - Get Product Special Dates.
            $todaysDate = date("Y-m-d");
            $FromDate = $_product->getSpecialFromDate();
            $ToDate = $_product->getSpecialToDate();

            // Date intervall
            $TheDate = date('Y-m-d');

            if ($_specialPrice) {
                if (($todaysDate > $FromDate) && ($todaysDate < $ToDate)) {
                    $isSpecial = true;
                } elseif (($todaysDate > $FromDate && empty($ToDate))) {
                    $isSpecial = true;
                }
            }

            return $isSpecial;
        }
    }

    private function getDeliveryText($optionValue)
    {
        if($optionValue->getData('sku')) {
            $skuIs = true;

            $sku = $optionValue->getData('sku');
            $_product = $this->getProductBySku($sku);
            $_stock = $this->getStockItem($_product->getId());
            $qtyStock = $_stock->getQty();

            // Data - Product Attributes
            $greenStat = intval($_product->getData('inventory_green_status_min')); // Returns int(0)
            $redStat = intval($_product->getData('inventory_red_status_max')); // Returns int(10)
            $yellowTxt = $_product->getData('inventory_yellow_status_text'); // Returns: string(46) "Fåtal kvar i lager. Leverans 2-7 arbetsdagar."
            $redTxt = $_product->getData('inventory_red_status_text'); // Returns: string(25) "Leverans 3-10 arbetsdagar"
            $greenTxt = $_product->getData('inventory_green_status_text'); // Returns: string(25) "Leverans 3-10 arbetsdagar"
            $RedExtraTxt = $_product->getData('red_xtra_status_text'); // Attribute: Meddelande om lång leveranstid
            // Date intervall
            $TheDate = date('Y-m-d');
            $RedExtraStatusStart = $_product->getData('red_xtra_status_start'); // Gets The correct date value.
            $RedExtraStatusEnd = $_product->getData('red_xtra_status_end'); // Gets The correct date value.
            $deliveryDataIs = false;

            if($_product->getData('notforsale')) {
                if($_product->isAvailable()) {
                    if($TheDate > $RedExtraStatusStart && $TheDate < $RedExtraStatusEnd){
                        $deliveryData = $RedExtraTxt;
                        $deliveryDataIs = true;

                    }
                    elseif ($qtyStock >= $greenStat) {
                        if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                            $deliveryData = $greenTxt;
                            $deliveryDataIs = true;
                        } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                            $deliveryData = $greenTxt;
                            $deliveryDataIs = true;
                        } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                            $deliveryData = '';

                        } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                            $deliveryData = '';
                        } else {
                            $deliveryData = $greenTxt;
                            $deliveryDataIs = true;
                        }
                    } // Green Stock
                    elseif ($qtyStock <= $redStat) {
                        if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                            $deliveryData = $redTxt;
                            $deliveryDataIs = true;
                        } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                            $deliveryData = $redTxt;
                            $deliveryDataIs = true;
                        } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                            $deliveryData = '';
                        } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                            $deliveryData = '';
                        } else {
                            $deliveryData = $redTxt;
                            $deliveryDataIs = true;
                        }
                    } // Red Stock

                    else {
                        if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                            $deliveryData = $yellowTxt;
                            $deliveryDataIs = true;

                        }

                        elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                            $deliveryData = $yellowTxt;
                            $deliveryDataIs = true;
                        } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                            $deliveryData = '';

                        } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                            $deliveryData = '';
                        } else {
                            $deliveryData = $yellowTxt;
                            $deliveryDataIs = true;
                        }
                    }
                    if ($deliveryDataIs == true) {
                        return $deliveryData;

                    }
                }
            }
        }
    }

    /**
     * Get html for visible part of swatch element
     *
     * @param Option $option
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface|\Magento\Catalog\Model\Product\Option\Value $optionValue
     * @return string
     */
    private function getOptionSwatchHtml($option, $optionValue)
    {
        $type = $optionValue->getBaseImageType() ? $optionValue->getBaseImageType() : 'text';
        $optionValue->getTitle() ? $label = $optionValue->getTitle() : $label = '';
        $store = $option->getProduct()->getStore();
        $value = $this->helper->getThumbImageUrl(
            $optionValue->getBaseImage(),
            Helper::IMAGE_MEDIA_ATTRIBUTE_SWATCH_IMAGE
        );
        if (!$value) {
            $value = $label;
        }

        if (!$optionValue->getPrice()) {
            $price = 0;
        } else {
            $price = $this->advancedPricingPrice->getPrice($option, $optionValue);
            if ($this->basePriceHelper->isPriceDisplayModeExcludeTax()) {
                $price = $this->basePriceHelper->getTaxPrice(
                    $option->getProduct(),
                    $price,
                    false
                );
            } else {
                $price = $this->basePriceHelper->getTaxPrice(
                    $option->getProduct(),
                    $price,
                    true
                );
            }
        }

        $deliveryText = $this->getDeliveryText($optionValue);
        $showSwatchTitle = $this->helper->isShowSwatchTitle();
        $showSwatchPrice = $this->helper->isShowSwatchPrice();
        $hiddenValues    = $this->hiddenDependentsModel->getHiddenValues($option->getProduct());
        $hiddenOptions   = $this->hiddenDependentsModel->getHiddenOptions($option->getProduct());
        $optionPrice = $this->getOptionPrice($optionValue);
        $optionIsSpecial = $this->optionIsSpecial($optionValue);
        $optionIsApplyRules = $this->optionisAppyRules($optionValue);
        $attributes = ' data-option-id="' . $option->getId() . '"' .
            ' data-option-type-id="' . $optionValue->getId() . '"' .
            ' data-option-type="' . $option->getType() . '"' .
            ' data-option-is-apply-rule="' . $optionIsApplyRules .  '"' .
            ' data-option-is-special="' . $optionIsSpecial .  '"' .
            ' data-option-final-price="' . $optionPrice .  '"' .
            ' data-option-value-price="' . $this->pricingHelper->currency($price, true, false) .  '"' .
            ' data-option-label="' . $label . '"' .
            ' data-option-delivery-time="' . $deliveryText . '"';
        ' data-option-price="' . $price . '"';

        $html = '<div class="mageworx-swatch-container"';
        if (in_array($optionValue->getOptionTypeId(), $hiddenValues)
            || in_array($option->getOptionId(), $hiddenOptions)
        ) {
            $html .= ' style="display:none"';
        }
        $html .= '>';

        switch ($type) {
            case 'text':
                $html .= '<div class="mageworx-swatch-option text"';
                $html .= $attributes;
                $html .= ' style="';
                $html .= ' max-width: ' . $this->helper->getTextSwatchMaxWidth() . 'px;';
                $html .= '">';
                $html .= $label;
                $html .= '</div>';
                if ($showSwatchPrice && $price) {
                    $html .= '<div class="mageworx-swatch-info"';
                    $html .= ' style="max-width: ' . ($this->helper->getTextSwatchMaxWidth() + 16) . 'px;">';
                    $html .= $this->pricingHelper->currencyByStore($price, $store);
                    $html .= '</div>';
                }
                break;
            case 'image':
            case 'color':
                $swatchWidth  = $this->helper->getSwatchWidth();
                $swatchHeight = $this->helper->getSwatchHeight();

                $swatchImgWidth  = $swatchWidth != 0 ? $swatchWidth : getimagesize($value)[0];
                $swatchImgHeight = $swatchHeight != 0 ? $swatchHeight : getimagesize($value)[1];

                $swatchColorWidth = $swatchWidth != 0 ? $swatchWidth : 64;
                $swatchColoHeight = $swatchHeight != 0 ? $swatchHeight : 64;

                $html .= '<div class="mageworx-swatch-option image"';
                $html .= $attributes;
                $html .= ' style="';
                if ($type == 'color') {
                    $html .= ' height: ' . $swatchColoHeight . 'px;';
                    $html .= ' width: ' . $swatchColorWidth . 'px;';
                } else {
                    $html .= ' height: ' . $swatchImgHeight . 'px;';
                    $html .= ' width: ' . $swatchImgWidth . 'px;';
                }
                $html .= ' background: url(' . $value . ') no-repeat center;';
                $html .= '"> ';
                $html .= '</div>';
                if ($showSwatchTitle) {
                    $html .= '<div class="mageworx-swatch-info"';
                    $html .= ' style="max-width: ' . ($swatchImgWidth + 2) . 'px;">';
                    $html .= $label;
                    $html .= '</div>';
                }
                if ($showSwatchPrice && $price) {
                    $html .= '<div class="mageworx-swatch-info"';
                    $html .= ' style="max-width: ' . ($swatchImgWidth + 2) . 'px;">';
                    $html .= $this->pricingHelper->currencyByStore($price, $store);
                    $html .= '</div>';
                }
                break;
            default:
                $html .= '<div class="mageworx-swatch-option"';
                $html .= $attributes;
                $html .= '>';
                $html .= $label;
                $html .= '</div>';
                break;
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Get html for hidden part of swatch element
     *
     * @param TypeSelect $subject
     * @return string
     */
    private function getOptionSwatchHiddenHtml($subject)
    {
        $option      = $subject->getOption();
        $configValue = $subject->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId());
        $store       = $subject->getProduct()->getStore();
        $hiddenValues  = $this->hiddenDependentsModel->getHiddenValues($option->getProduct());
        $hiddenOptions = $this->hiddenDependentsModel->getHiddenOptions($option->getProduct());
        $require     = $option->getIsRequire() && !in_array($option->getOptionId(), $hiddenOptions) ? ' required' : '';
        $extraParams = '';
        /** @var \Magento\Framework\View\Element\Html\Select $select */
        $select = $subject->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            [
                'id' => 'select_' . $option->getId()
            ]
        );
        if ($option->getType() == Option::OPTION_TYPE_DROP_DOWN && $option->getIsSwatch()) {
            $select->setName('options[' . $option->getId() . ']')->addOption('', __('-- Please Select --'));
            $select->setClass($require . ' mageworx-swatch hidden product-custom-option admin__control-select');
        } else {
            $select->setName('options[' . $option->getId() . '][]');
            $select->setClass(
                $require
                . ' mageworx-swatch hidden product-custom-option multiselect admin__control-multiselect '
            );
        }
        /** @var \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface $value */
        foreach ($option->getValues() as $value) {
            $priceStr = '';
            if (in_array($value->getOptionTypeId(), $hiddenValues)
                || in_array($option->getOptionId(), $hiddenOptions)
            ) {
                $select->addOption(
                    $value->getOptionTypeId(),
                    $value->getTitle() . ' ' . strip_tags($priceStr) . '',
                    [
                        'price' => $this->pricingHelper->currencyByStore($value->getPrice(), $store, false),
                        'style' => "display:none"
                    ]
                );
            } else {
                $select->addOption(
                    $value->getOptionTypeId(),
                    $value->getTitle() . ' ' . strip_tags($priceStr) . '',
                    ['price' => $this->pricingHelper->currencyByStore($value->getPrice(), $store, false)]
                );
            }
        }
        if ($option->getType() == Option::OPTION_TYPE_MULTIPLE && $option->getIsSwatch()) {
            $extraParams = ' multiple="multiple"';
        }
        $extraParams .= ' data-selector="' . $select->getName() . '"';
        $select->setExtraParams($extraParams);

        if ($configValue) {
            $select->setValue($configValue);
        }

        return $select->getHtml();
    }
}
