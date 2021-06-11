<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Plugin\Order;

/**
 *
 */
class ShippingInformationManagement
{

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data
     */
    private $helper;

    /**
     * @var \Mediastrategi\Unifaun\Model\Cache\Checkout
     */
    private $checkoutCache;

    /**
     * @param \Mediastrategi\Unifaun\Helper\Data $dataHelper
     * @param \Mediastrategi\Unifaun\Model\Cache\Checkout $checkoutCache
     */
    public function __construct(
        \Mediastrategi\Unifaun\Helper\Data $dataHelper,
        \Mediastrategi\Unifaun\Model\Cache\Checkout $checkoutCache
    ) {
        $this->helper = $dataHelper;
        $this->checkoutCache = $checkoutCache;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param \Magento\Checkout\Model\ShippingInformation $addressInformation
     * @return mixed
     */
    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Model\ShippingInformation $addressInformation
    ) {
        $this->helper->log(__(
            '%1 extension-attributes for shipping-address: %2, shipping-address before process: %3',
            __METHOD__,
            print_r($addressInformation->getShippingAddress()->getExtensionAttributes(), true),
            print_r($addressInformation->debug(), true)
        ));

        if ($extensionAttributes = $addressInformation->getShippingAddress()->getExtensionAttributes()) {
            $orderAttributes = $addressInformation->getShippingAddress()->getOrderAttributes();
            if ($pickUpLocationId = $extensionAttributes->getPickUpLocationId()) {
                $orderAttributes['pick_up_location_id'] = $pickUpLocationId;
            }
            if ($pickUpLocationName = $extensionAttributes->getPickUpLocationName()) {
                $orderAttributes['pick_up_location_name'] = $pickUpLocationName;
            }
            if ($pickUpLocationAddress1 = $extensionAttributes->getPickUpLocationAddress1()) {
                $orderAttributes['pick_up_location_address'] = $pickUpLocationAddress1;
            }
            if ($pickUpLocationZipCode = $extensionAttributes->getPickUpLocationZipCode()) {
                $orderAttributes['pick_up_location_zip_code'] = $pickUpLocationZipCode;
            }
            if ($pickUpLocationCity = $extensionAttributes->getPickUpLocationCity()) {
                $orderAttributes['pick_up_location_city'] = $pickUpLocationCity;
            }
            if ($pickUpLocationCountry = $extensionAttributes->getPickUpLocationCountry()) {
                $orderAttributes['pick_up_location_country'] = $pickUpLocationCountry;
            }

            $addressInformation->getShippingAddress()->setOrderAttributes($orderAttributes);
            $this->helper->log(__(
                '%1 order-attributes for shipping-address after adding: %2',
                __METHOD__,
                print_r($addressInformation->getShippingAddress()->getOrderAttributes(), true)
            ));
        } else {
            /*
            error_log(sprintf(
                'Found no initial order attributes %s',
                print_r($_SERVER, true)
            ));
            */
        }

        $this->helper->log(__(
            '%1 shipping-address after process: %2',
            __METHOD__,
            print_r($addressInformation->debug(), true)
        ));

        if ($orderAttributes = $addressInformation->getShippingAddress()->getOrderAttributes()) {
            $this->checkoutCache->save(
                json_encode($orderAttributes),
                $cartId,
                [\Mediastrategi\Unifaun\Model\Cache\Checkout::CACHE_TAG],
                \Mediastrategi\Unifaun\Model\Cache\Checkout::CACHE_EXPIRATION
            );
            /*
            error_log('Saved checkout cache for');
            error_log(print_r($_SERVER, true));
            error_log(print_r($orderAttributes, true));
            exit;
            */
        } else {
            /*
            error_log('Found no final order attributes');
            error_log(print_r($_SERVER, true));
            exit;
            */
        }

        return $proceed($cartId, $addressInformation);
    }
}
