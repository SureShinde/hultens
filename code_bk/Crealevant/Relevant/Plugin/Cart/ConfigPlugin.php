<?php

namespace Crealevant\Relevant\Plugin\Cart;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigPlugin extends  \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var UrlInterface
     */
    protected $scopeConfig;

    /**
     * ConfigPlugin constructor.
     * @param UrlInterface $url
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(
        \Magento\Checkout\Block\Cart\Sidebar $subject,
        array $result
    ) {
        $result['settings'] = $this->scopeConfig->getValue('settings/minicart/type');
        $result['amount_left'] = $this->scopeConfig->getValue('cart/general/amount');
        return $result;
    }
}
