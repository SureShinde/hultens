<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Plugin;

/**
 *
 */
class ShippingMethodConverterPlugin
{

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        \Mediastrategi\Unifaun\Helper\Data $helper,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->helper = $helper;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel The rate model.
     * @param string $quoteCurrencyCode The quote currency code.
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface|void
     * @see \Mediastrategi\UNIFAUN\Model\Carrier->collectRates()
     * @see \Magento\Quote\Model\Quote\Address->getShippingRateByCode()
     * @see \Magento\Quote\Model\Quote\Address->getShippingRatesCollection()
     * @see \Magento\Quote\Model\Quote->requestShippingRates()
     * @see \Magento\Quote\Model\Quote\Address\Rate->importShippingRate()
     */
    public function aroundModelToDataObject($subject, $proceed, $rateModel, $quoteCurrencyCode)
    {
        if ($this->helper->isEnabled()) {
            $result = $proceed($rateModel, $quoteCurrencyCode);
            /** @var \Magento\Quote\Api\Data\ShippingMethodInterface $data */
            $extensionAttributes = $result->getExtensionAttributes();
            if (!isset($extensionAttributes)) {
                $extensionAttributes = $this->extensionAttributesFactory->create(
                    '\Magento\Quote\Api\Data\ShippingMethodInterface'
                );
            }
            $extensionAttributes->setMsunifaunAgents($rateModel->getData('msunifaun_agents'));
            $extensionAttributes->setMsunifaunCarrier($rateModel->getData('msunifaun_carrier'));
            $result->setExtensionAttributes($extensionAttributes);
            return $result;
        } else {
            return $proceed(
                $rateModel,
                $quoteCurrencyCode
            );
        }
    }
}
