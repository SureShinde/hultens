<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Plugin\Order;

/**
 *
 */
class Save
{

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data $helper
     */
    private $helper;

    /**
     * @var \Mediastrategi\Unifaun\Model\Cache\Checkout
     */
    private $checkoutCache;

    /**
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     * @param \Mediastrategi\Unifaun\Helper\Session $checkoutCache
     */
    public function __construct(
        \Mediastrategi\Unifaun\Helper\Data $helper,
        \Mediastrategi\Unifaun\Model\Cache\Checkout $checkoutCache
    ) {
        $this->helper = $helper;
        $this->checkoutCache = $checkoutCache;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $quoteId = $order->getQuoteId();
        if ($quoteId) {
            $updated = 0;

            if ($this->checkoutCache->test($quoteId)) {
                $extensionAttributes = $this->checkoutCache->load($quoteId);
                try {
                    $extensionAttributes = json_decode($extensionAttributes, true);
                } catch (\Exception $e) {
                    $extensionAttributes = false;
                }
                if (!empty($extensionAttributes)) {
                    if (!empty($extensionAttributes['pick_up_location_id'])) {
                        $order->setPickUpLocationId($extensionAttributes['pick_up_location_id']);
                        $updated++;
                    }
                    if (!empty($extensionAttributes['pick_up_location_name'])) {
                        $order->setPickUpLocationName($extensionAttributes['pick_up_location_name']);
                        $updated++;
                    }
                    if (!empty($extensionAttributes['pick_up_location_address'])) {
                        $order->setPickUpLocationAddress($extensionAttributes['pick_up_location_address']);
                        $updated++;
                    }
                    if (!empty($extensionAttributes['pick_up_location_zip_code'])) {
                        $order->setPickUpLocationZipCode($extensionAttributes['pick_up_location_zip_code']);
                        $updated++;
                    }
                    if (!empty($extensionAttributes['pick_up_location_city'])) {
                        $order->setPickUpLocationCity($extensionAttributes['pick_up_location_city']);
                        $updated++;
                    }
                    if (!empty($extensionAttributes['pick_up_location_country'])) {
                        $order->setPickUpLocationCountry($extensionAttributes['pick_up_location_country']);
                        $updated++;
                    }
                }

                // error_log('Found checkout cache for');
                // error_log(print_r($_SERVER, true));
                // error_log(print_r($extensionAttributes, true));
                // exit;
            } else {
                // error_log('Found no checkout cache for');
                // error_log(print_r($_SERVER, true));
                // exit;
            }

            if ($updated) {
                $order->save();
            }

            /*
            error_log(__(
                '%1 updated %2 order attributes from cache',
                __METHOD__,
                $updated
            ));
            error_log(print_r($extensionAttributes, true));
            */
            // exit;

            $this->helper->log(__(
                '%1 updated %2 order attributes from cache',
                __METHOD__,
                $updated
            ));
        }
        return $order;
    }

}
