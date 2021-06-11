<?php
namespace Crealevant\Hultencheckout\Api;

use Crealevant\Hultencheckout\Api\Data\ItemUpdateInterface;

interface CartItemsInterface
{
    /**
     * Update quantity of item in cart
     *
     * @param string $cartId
     * @param ItemUpdateInterface $itemUpdate
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function updateItemQty($cartId, ItemUpdateInterface $itemUpdate);
}