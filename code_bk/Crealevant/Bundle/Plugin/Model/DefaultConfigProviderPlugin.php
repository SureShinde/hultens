<?php

namespace Crealevant\Bundle\Plugin\Model;

use Magento\Catalog\Model\ProductRepository as ProductRepository;
use Magento\Checkout\Model\DefaultConfigProvider as MagentoDefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Model\AbstractModel;

class DefaultConfigProviderPlugin extends AbstractModel
{
    protected $checkoutSession;

    protected $_productRepository;

    public function __construct(
        CheckoutSession $checkoutSession,
        ProductRepository $productRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->_productRepository = $productRepository;
    }

    public function afterGetConfig(MagentoDefaultConfigProvider $subject, array $result)
    {
        $items = $result['totalsData']['items'];
        foreach ($items as $index => $item) {
            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $product = $this->_productRepository->getById($quoteItem->getProduct()->getId());
            $result['quoteItemData'][$index]['shipping_info'] = $this->getTextShippingInfo($product);
        }
        return $result;
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
            $textShippingInfo = $RedExtraTxt;
        } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
            $textShippingInfo = $RedExtraTxt;
        } else {
            $textShippingInfo = '';
        }

        // All the above variables returns the correct data, dates and label.
        if (($TheDate > $RedExtraStatusStart && $TheDate < $RedExtraStatusEnd) || empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
            $textShippingInfo = $RedExtraTxt;
        }

        if ($product->getData('notforsale') && $product->isAvailable()) {
            if ($qtyStock >= $greenStat) {
                // Remove Delivery Text Depending on Setting for Red Extra Status Start And Red Extra Status End
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = $greenTxt;
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = $greenTxt;
                } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                    $textShippingInfo = '';
                } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                    $textShippingInfo = '';
                } else {
                    $textShippingInfo = $greenTxt;
                }
            } elseif ($qtyStock <= $redStat) {
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = $redTxt;
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = $redTxt;
                } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                    $textShippingInfo = '';
                } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                    $textShippingInfo = '';
                } else {
                    $textShippingInfo = $redTxt;
                }
            } else {
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = $yellowTxt;
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = $yellowTxt;
                } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                    $textShippingInfo = '';
                } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                    $textShippingInfo = '';
                } else {
                    $textShippingInfo = $yellowTxt;
                }
            }
        }

        return $textShippingInfo;
    }
}