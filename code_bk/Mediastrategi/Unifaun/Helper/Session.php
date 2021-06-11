<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Helper;

/**
 *
 */
class Session extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var string
     */
    const SESSION_KEY = 'unifaun_order_attributes';

    /**
     * @internal
     * @var \Magento\Framework\Session\Storage
     */
    protected $_session;

    /**
     *
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Session\Storage $sessionStorage
    ) {
        $this->_session = $sessionStorage;
        parent::__construct($context);
    }

    /**
     * @param array $orderAttributes
     */
    public function setOrderAttributesToSession($orderAttributes)
    {
        $existsOrderAttributes = $this->getOrderAttributesFromSession();
        $orderAttributes = $orderAttributes ?: [];
        $orderAttributes = $existsOrderAttributes
            ? array_merge($existsOrderAttributes, $orderAttributes)
            : $orderAttributes;
        $orderAttributes = $orderAttributes ?: [];
        $this->_session->setData(
            self::SESSION_KEY,
            $orderAttributes
        );
    }

    /**
     *
     */
    public function clearOrderAttributesFromSession()
    {
        $this->_session->setData(
            self::SESSION_KEY,
            []
        );
    }

    /**
     * @return mixed
     */
    public function getOrderAttributesFromSession()
    {
        return $this->_session->getData(self::SESSION_KEY);
    }
}
