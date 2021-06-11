<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Crealevant\Relevant\Block;

use Magento\Catalog\Block\Product\View\Options\Type\Select;

class CustomOptionQuantity extends \MageWorx\OptionSwatches\Plugin\Product\View\Options\Type\Select
{


    /**
     * @param Select $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetValuesHtml(Select $subject, \Closure $proceed)
    {
        $result = $proceed();

        if (!$this->helper->isQtyInputEnabled() || !$result) {
            return $result;
        }

        $option = $subject->getOption();

        $optionsQty = [];
        if ($this->request->getControllerName() != 'product') {
            $quoteItemId = (int)$this->request->getParam('id');
            if ($quoteItemId) {
                if ($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
                    $quoteItem = $this->backendQuoteSession->getQuote()->getItemById($quoteItemId);
                } else {
                    $quoteItem = $this->cart->getQuote()->getItemById($quoteItemId);
                }
                if ($quoteItem) {
                    $buyRequest = $quoteItem->getBuyRequest();
                    if ($buyRequest) {
                        $optionsQty = $buyRequest->getOptionsQty();
                    }
                }
            }
        }

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;

        $this->mbString->setEncoding('UTF-8', 'html-entities');
        $result = $this->mbString->convert($result);

        $dom->loadHTML($result);
        $body = $dom->documentElement->firstChild;

        if ($option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_CHECKBOX && $option->getQtyInput()) {
            $count = 1;
            foreach ($option->getValues() as $value) {
                $count++;

                $optionValueQty = $this->getOptionQty($optionsQty, $option, $value->getOptionTypeId());

                $qtyInput = '<div class="label-qty" style="display: inline-block; padding: 5px; margin-left: 3em"><b>Antal: </b>';
                $qtyInput .= '<input name="options_qty['.$option->getId().']['.$value->getOptionTypeId().']"';
                $qtyInput .= ' id="options_' . $option->getId() .'_'. $value->getOptionTypeId().'_qty"';
                $qtyInput .= ' class="qty mageworx-option-qty" type="number" value="'.$optionValueQty.'" min="0" disabled';
                $qtyInput .= ' style="width: 3em; text-align: center; vertical-align: middle;"';
                $qtyInput .= ' data-parent-selector="options['.$option->getId().']['.$value->getOptionTypeId().']"';
                $qtyInput .= ' />';
                $qtyInput .= '</div>';

                $tpl = new \DOMDocument('1.0', 'UTF-8');
                $tpl->loadHtml($qtyInput);

                $xpath = new \DOMXPath($dom);
                $idString = 'options_'.$option->getId().'_'.$count;
                $input = $xpath->query("//*[@id='$idString']")->item(0);

                $input->setAttribute('style', 'vertical-align: middle');
                $input->parentNode->appendChild($dom->importNode($tpl->documentElement, true));
            }
        } else {
            if ($option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_MULTIPLE ||
                $option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO ||
                !$option->getQtyInput()
            ) {

                $qtyInputContainerStart = "";
                $qtyInputContainerEnd = "";
                $qtyInput = '<input name="options_qty[' . $option->getId() . ']" id="options_' . $option->getId() . '"';
                $qtyInput .= ' class="qty mageworx-option-qty" type="hidden" value="1"';
                $qtyInput .= ' style="width: 3em; text-align: center; vertical-align: middle;"';
                $qtyInput .= ' data-parent-selector="options[' . $option->getId() . ']"';
                $qtyInput .= ' />';
            } else {
                $optionQty = $this->getOptionQty($optionsQty, $option, $option->getId());

                $qtyInputContainerStart = '<div class="qty-custom-options-wrapper">';
                $qtyInputContainerStart .= '<span class="input-group-btn incdec">
                <button type="button" class="btn btn-default btn-qty btn-minus">
                <span class="ion-minus-round"></span>
                </button>
                </span>';

                $qtyInput = '<input name="options_qty[' . $option->getId() . ']" id="options_' . $option->getId() . '"';
                $qtyInput .= ' class="item-qty cart-item-qty incdec qty mageworx-option-qty" type="number" value="' . $optionQty . '" min="0" disabled';
                $qtyInput .= ' style="width: 3em; text-align: center; vertical-align: middle;"';
                $qtyInput .= ' data-parent-selector="options[' . $option->getId() . ']"';
                $qtyInput .= ' />';

                $qtyInputContainerEnd = '<span class="input-group-btn incdec">
                <button type="button" class="btn btn-default btn-qty btn-plus">
                <span class="ion-plus-round"></span>
                </button>
                </span>';
                $qtyInputContainerEnd .= '</div>';
            }

            $tpl = new \DOMDocument();
            $tpl->loadHtml($qtyInputContainerStart . $qtyInput . $qtyInputContainerEnd);
            $body->appendChild($dom->importNode($tpl->documentElement, true));
        }

        libxml_clear_errors();

        $resultBody = $dom->getElementsByTagName('body')->item(0);
        $result = $this->getInnerHtml($resultBody);

        return $result;
    }
}
