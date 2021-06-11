<?php

namespace Crealevant\Hultencheckout\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Bundle;

class AdditionalConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{

    protected $checkoutSession;
    protected $cartRepository;
    protected $productRepository;
    protected $imageHelper;
    protected $urlBuilder;
    protected $productFactory;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->productFactory = $productFactory;
    }


    public function getConfig()
    {
        $quoteId = $this->checkoutSession->getQuote()->getId();
        $itemData = [];
        $cart = $this->cartRepository->get($quoteId);
        foreach ($cart->getAllItems() as $cartItem) {
            $product = $this->productRepository->getById($cartItem->getProductId());
            if ($cartItem->getParentItemId()) {
                // Bundle children product
                $shippingInfo = $this->getTextShippingInfo($product);

                $productObj = $this->productFactory->create()->load($product->getId());

                $itemData[$cartItem->getParentItemId()]['items'][] = [
                    'id' => $cartItem->getId(),
                    'image' => $this->imageHelper->init($product,
                        'product_page_image_small')->setImageFile($product->getData('thumbnail'))->resize(380)->getUrl(),
                    'name' => $cartItem->getName(),
                    'shipping_info' => $this->getTextShippingInfo($product),
                    'qty' => $cartItem->getQty(),
                    'red_xtra_status_text' => $productObj->getRedXtraStatusText() && $this->checkIsBundle($productObj) ? '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $productObj->getRedXtraStatusText() : null

                ];
            } else {
                if (!$cartItem->getHasChildren()) {
                    // Standalone simple product
                    $shippingInfo = $this->getTextShippingInfo($product);
                    $itemData[$cartItem->getId()] = [
                        'image' => $this->imageHelper->init($product,
                            'product_page_image_small')->setImageFile($product->getData('thumbnail'))->resize(380)->getUrl(),
                        'shipping_info' => $shippingInfo['text']
                    ];
                } else {
                    // Bundle parent product
                    $shippingInfoType = 0;
                    $shippingInfoText = '';

                    foreach ($cart->getAllItems() as $newCartItem) {
                        $newProduct = $this->productRepository->getById($newCartItem->getProductId());

                        if ($newCartItem->getParentItemId() == $cartItem->getId()) {
                            // Configurable children product
                            $shippingInfo = $this->getTextShippingInfo($newProduct);
                            if ($shippingInfo['type'] == $shippingInfoType) {
                                if (strcmp($shippingInfoText, $shippingInfo['text']) < 0) {
                                    $shippingInfoText = $shippingInfo['text'];
                                }
                            } else {
                                if ($shippingInfo['type'] > $shippingInfoType) {
                                    $shippingInfoType = $shippingInfo['type'];
                                    $shippingInfoText = $shippingInfo['text'];
                                }
                            }
                        }
                    }
                    $productObj = $this->productFactory->create()->load($product->getId());
                    $itemData[$cartItem->getId()] = [
                        'image' => $this->imageHelper->init($product,
                            'product_page_image_small')->setImageFile($product->getData('thumbnail'))->resize(380)->getUrl(),
                        'shipping_info' => $shippingInfoText,
                        'red_xtra_status_text' => $productObj->getRedXtraStatusText() && $this->checkIsBundle($productObj) ? '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $productObj->getRedXtraStatusText() : null
                    ];
                }
            }
        }
        $output['custom_options'] = $itemData;
        $output['url_remove_item'] = $this->getRemoveItem();
        return $output;
    }

    private function getRemoveItem()
    {
        return $this->urlBuilder->getUrl('hultenscheckout/cart/removeitem');
    }

    private function getTextShippingInfo($product)
    {
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
                'text' => '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $RedExtraTxt
            ];

        } elseif (empty($RedExtraStatusEnd) && $TheDate > $RedExtraStatusStart) {
            $textShippingInfo = [
                'type' => '3',
                'text' => '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $RedExtraTxt
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
                'text' => '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $RedExtraTxt
            ];
        }

        if ($product->getData('notforsale') && $product->isAvailable()) {
            if ($qtyStock >= $greenStat) {
                // Remove Delivery Text Depending on Setting for Red Extra Status Start And Red Extra Status End
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = [
                        'type' => '1',
                        'text' => '<span style="display: inline-block; background: green; border: 1px solid green; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $greenTxt
                    ];
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = [
                        'type' => '1',
                        'text' => '<span style="display: inline-block; background: green; border: 1px solid green; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $greenTxt
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
                        'text' => '<span style="display: inline-block; background: green; border: 1px solid green; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $greenTxt
                    ];
                }
            } elseif ($qtyStock <= $redStat) {
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = [
                        'type' => '3',
                        'text' => '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $redTxt
                    ];
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = [
                        'type' => '3',
                        'text' => '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $redTxt
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
                        'text' => '<span style="display: inline-block; background: red; border: 1px solid red; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $redTxt
                    ];
                }
            } else {
                if ($RedExtraStatusStart == null && $RedExtraStatusEnd == null) {
                    $textShippingInfo = [
                        'type' => '2',
                        'text' => '<span style="display: inline-block; background: yellow; border: 1px solid yellow; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $yellowTxt
                    ];
                } elseif ($TheDate > $RedExtraStatusStart == false && $TheDate < $RedExtraStatusEnd == false) {
                    $textShippingInfo = [
                        'type' => '2',
                        'text' => '<span style="display: inline-block; background: yellow; border: 1px solid yellow; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $yellowTxt
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
                        'text' => '<span style="display: inline-block; background: yellow; border: 1px solid yellow; padding: 0px 4px; border-radius: 25px; height: 10px; margin-right: 5px;"></span>' . $yellowTxt
                    ];
                }
            }
        }

        return $textShippingInfo;
    }

    public function checkIsBundle($product)
    {
        return $product->getTypeID() == "bundle" ? true : false;
    }
}