<?php

namespace Crealevant\Unifaun\Plugin;

class ShippingMethodConverterPlugin
{
    /**
     * @var \Mediastrategi\Unifaun\Helper\Data
     */
    private $helper;
    /**
     * @var ShippingMethodExtensionFactory
     */
    private $extensionAttributesFactory;

    private $storeManager;

    /**
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mediastrategi\Unifaun\Helper\Data $helper,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * Add delivery date information to the carrier data object
     * @param ShippingMethodConverter $subject
     * @param ShippingMethodInterface $result
     * @return ShippingMethodInterface
     */
    public function aroundModelToDataObject($subject, $proceed, $rateModel, $quoteCurrencyCode)
    {
        if ($this->helper->isEnabled()) {
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
            $result = $proceed($rateModel, $quoteCurrencyCode);

            /** @var \Magento\Quote\Api\Data\ShippingMethodInterface $data */
            $extensionAttributes = $result->getExtensionAttributes();
            if (!isset($extensionAttributes)) {
                $extensionAttributes = $this->extensionAttributesFactory->create(
                    '\Magento\Quote\Api\Data\ShippingMethodInterface'
                );
            }
            $result->setExtensionAttributes($extensionAttributes);
            $extensionAttributes->setMsunifaunDescription($rateModel->getData('msunifaun_description'));
            if($rateModel->getData('msunifaun_image')){
                $extensionAttributes->setMsunifaunImage('<img src="'.$mediaUrl . $rateModel->getData('msunifaun_image') .'" />');
            }

            return $result;
        } else {
            return $proceed(
                $rateModel,
                $quoteCurrencyCode
            );
        }
    }
}