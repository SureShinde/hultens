<?php
/**
 * Created by PhpStorm.
 * User: henrikj
 * Date: 30/11/16
 * Time: 08:34
 */

namespace Crealevant\Relevant\Plugin;


use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class CustomerDataCart
 * @package Crealevant\Simplecheckout\Plugin
 */
class CustomerDataCart
{

    protected $checkoutSession;

    /** @var Quote $quote */
    protected $quote;

    protected $helper;

    /**
     * @var UrlInterface
     */
    protected $scopeConfig;


    /**
     * ItemConverterAddSubPricePlugin constructor.
     * @param Session $session
     * @param Data $checkoutHelper
     * @internal param Registry $registry
     * @internal param Data $helper
     */
    public function __construct(
       Session $session,
       Data $checkoutHelper,
       ScopeConfigInterface $scopeConfig
    ) {
        $this->checkoutSession = $session;
        $this->helper = $checkoutHelper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSectionData($subject, $result)
    {
        $this->quote = $this->checkoutSession->getQuote();
        $totals = $this->quote->getTotals();
        $result['tax_total'] = $this->helper->formatPrice($totals['tax']->getValue());
        $result['minicart_freefreight'] = $this->helper->formatPrice($this->_getLeftForFreeFreight());

        for($i = 0; $i < count($result['items']); $i++) {
            $item = $this->quote->getItemById($result['items'][$i]['item_id']);
            $result["items"][$i]['subscriber_price'] = $this->helper->formatPrice($item->getSubscriberPrice());
            $result["items"][$i]['non_subscriber_price'] = $this->helper->formatPrice($item->getNonSubscriberPrice());
            if ($item->getSubscriberPrice() > $item->getPriceInclTax()) {
                $result["items"][$i]['subscriber_use_special'] = true;
            }
            if ($item->getNonSubscriberPrice() > $item->getPriceInclTax()) {
                $result["items"][$i]['price_use_special'] = true;
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    private function _getLeftForFreeFreight()
    {
        $subTotal = $this->quote->getTotals()['subtotal']->getValueInclTax();
        $tofreefreight = $this->scopeConfig->getValue('cart/general/amount') - $subTotal;
        if ($tofreefreight < 0) {
            $tofreefreight = 0;
        }

        return $tofreefreight;
    }
}