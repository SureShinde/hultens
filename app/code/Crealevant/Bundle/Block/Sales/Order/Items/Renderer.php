<?php

namespace Crealevant\Bundle\Block\Sales\Order\Items;

use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Bundle\Block\Sales\Order\Items\Renderer as ItemRenderer;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Renderer extends ItemRenderer
{
    private $serializer;

    protected $productRepository;

    public function __construct(
        Context $context,
        StringUtils $string,
        OptionFactory $productOptionFactory,
        array $data = [],
        Json $serializer = null,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context, $string, $productOptionFactory, $data, $serializer);
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Json::class);
        $this->productRepository = $productRepository;
    }

    public function getTextShippingInfo($itemId)
    {
        $product = $this->productRepository->getById($itemId);
        $textShippingInfo = [];

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
        $yellowTxt = $product->getResource()->getAttribute('inventory_yellow_status_text')->getFrontend()->getValue($product); // Returns: string(46) "FÃ¥tal kvar i lager. Leverans 2-7 arbetsdagar."
        $redTxt = $product->getResource()->getAttribute('inventory_red_status_text')->getFrontend()->getValue($product); // Returns: string(25) "Leverans 3-10 arbetsdagar"
        $greenTxt = $product->getResource()->getAttribute('inventory_green_status_text')->getFrontend()->getValue($product); // Returns: string(25) "Leverans 3-10 arbetsdagar"

        // Stock qty
        $qtyStock = $product->getExtensionAttributes()->getStockItem()->getQty();

        // Date intervall
        $TheDate = date('Y-m-d');

        $RedExtraStatusStart = $product->getData('red_xtra_status_start'); // Gets The correct date value.
        $RedExtraStatusEnd = $product->getData('red_xtra_status_end'); // Gets The correct date value.

        $RedExtraTxt = $product->getResource()->getAttribute('red_xtra_status_text')->getFrontend()->getValue($product); // Attribute: Meddelande om lÃ¥ng leveranstid

        if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
            $textShippingInfo = [
                'type' => '0',
                'text' => ''
            ];
        } elseif ($TheDate > $RedExtraStatusStart && $TheDate < $RedExtraStatusEnd) {
            $textShippingInfo = [
                'type' => '3',
                'text' => $RedExtraTxt
            ];

        } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
            $textShippingInfo = [
                'type' => '3',
                'text' => $RedExtraTxt
            ];
        } else {
            $textShippingInfo = [
                'type' => '0',
                'text' => ''
            ];
        }

        // All the above variables returns the correct data, dates and label.
        if (($TheDate > $RedExtraStatusStart && $TheDate < $RedExtraStatusEnd) || empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
            $textShippingInfo = [
                'type' => '3',
                'text' => $RedExtraTxt
            ];
        }

        if ($product->getData('notforsale') && $product->isAvailable()) {
            if ($qtyStock >= $greenStat) {
                // Remove Delivery Text Depending on Setting for Red Extra Status Start And Red Extra Status End
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = [
                        'type' => '1',
                        'text' => $greenTxt
                    ];
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = [
                        'type' => '1',
                        'text' => $greenTxt
                    ];
                } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                    $textShippingInfo = [
                        'type' => '0',
                        'text' => ''
                    ];
                    $iconShippingInfo = '';
                } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                    $textShippingInfo = [
                        'type' => '0',
                        'text' => ''
                    ];
                    $iconShippingInfo = '';
                } else {
                    $textShippingInfo = [
                        'type' => '1',
                        'text' => $greenTxt
                    ];
                }
            } elseif ($qtyStock <= $redStat) {
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = [
                        'type' => '3',
                        'text' => $redTxt
                    ];
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = [
                        'type' => '3',
                        'text' => $redTxt
                    ];
                } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                    $textShippingInfo = [
                        'type' => '0',
                        'text' => ''
                    ];
                    $iconShippingInfo = '';
                } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                    $textShippingInfo = [
                        'type' => '0',
                        'text' => ''
                    ];
                    $iconShippingInfo = '';
                } else {
                    $textShippingInfo = [
                        'type' => '3',
                        'text' => $redTxt
                    ];
                }
            } else {
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = [
                        'type' => '2',
                        'text' => $yellowTxt
                    ];
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = [
                        'type' => '2',
                        'text' => $yellowTxt
                    ];
                } elseif ($TheDate > $RedExtraStatusStart == true && $TheDate < $RedExtraStatusEnd == true) {
                    $textShippingInfo = [
                        'type' => '0',
                        'text' => ''
                    ];
                    $iconShippingInfo = '';
                } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
                    $textShippingInfo = [
                        'type' => '0',
                        'text' => ''
                    ];
                    $iconShippingInfo = '';
                } else {
                    $textShippingInfo = [
                        'type' => '2',
                        'text' => $yellowTxt
                    ];
                }
            }
        }

        return $textShippingInfo;
    }
}