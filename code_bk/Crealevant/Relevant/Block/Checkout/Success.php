<?php
namespace Crealevant\Relevant\Block\Checkout;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    protected $_checkoutSession;
    protected $_orderFactory;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        $this->_orderFactory = $orderFactory;
        parent::__construct(
            $context,
            $checkoutSession,
            $orderConfig,
            $httpContext,
            $data
        );

    }

    /**
     * @return int
     */
    public function getGrandTotal()
    {
        if ($this->_checkoutSession->getLastRealOrderId()) {
            $order = $this->_orderFactory->create()->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());
            return $order->getGrandTotal();
        }
    }
}