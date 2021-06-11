<?php

namespace Crealevant\KlarnaKco\Override\Block;

use Klarna\Base\Model\OrderRepository;
use Klarna\Kco\Model\Api\Factory;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Klarna\Kco\Block\Success as KcoSuccess;

/**
 *
 * @api
 */
class Success extends KcoSuccess
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Success constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param Factory $factory
     * @param OrderRepository $kcoOrderRepository
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Factory $factory,
        OrderRepository $kcoOrderRepository,
        array $data
    ) {
        parent::__construct($context, $checkoutSession, $factory, $kcoOrderRepository, $data);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return array
     */
    public function getOrderRevenue()
    {
        $order = $this->checkoutSession->getLastRealOrder();

        return [
            'revenue' => $order->getGrandTotal(),
            'currency' => $order->getOrderCurrencyCode()
        ];
    }
}
