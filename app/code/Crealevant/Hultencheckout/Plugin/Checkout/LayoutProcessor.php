<?php

namespace Crealevant\Hultencheckout\Plugin\Checkout;

class LayoutProcessor
{
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, $result)
    {
        if (isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['afterMethods']['children']['discount']
        )) {
            $discountLayout = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['afterMethods']['children']['discount'];
            $discountLayout['displayArea'] = 'coupon';
            $result['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['children']['coupon'] = $discountLayout;
            return $result;
        }
    }
}
