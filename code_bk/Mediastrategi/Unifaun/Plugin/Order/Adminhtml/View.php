<?php
/**
 * @author Christian Johansson <christian@mediastrategi.se>
 */

/**
 *
 */
namespace Mediastrategi\Unifaun\Plugin\Order\Adminhtml;

/**
 *
 */
class View
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param string $result
     */
    public function afterToHtml(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        $result)
    {
        if ($order = $subject->getOrder()) {
            if ($order->getPickUpLocationId()) {
                $result .= '<section id="msunifaun-admintml-order-view" class="admin__page-section order-addresses"><div class="admin__page-section-title">'
                        . '<span class="title">' . __('Unifaun Online Information') . '</span></div><div class="admin__page-section-content"><dl>';

                if ($order->getPickUpLocationId()) {
                    $result .= '<dt>' . __('Custom Pick Up Location Id') . '</dt><dd>' . $order->getPickUpLocationId() . '</dd>';
                }
                if ($order->getPickUpLocationName()) {
                    $result .= '<dt>' . __('Custom Pick Up Location Name') . '</dt><dd>' . $order->getPickUpLocationName() . '</dd>';
                }
                if ($order->getPickUpLocationAddress()) {
                    $result .= '<dt>' . __('Custom Pick Up Location Address') . '</dt><dd>' . $order->getPickUpLocationAddress() . '</dd>';
                }
                if ($order->getPickUpLocationZipCode()) {
                    $result .= '<dt>' . __('Custom Pick Up Location Zip Code') . '</dt><dd>' . $order->getPickUpLocationZipCode() . '</dd>';
                }
                if ($order->getPickUpLocationCity()) {
                    $result .= '<dt>' . __('Custom Pick Up Location City') . '</dt><dd>' . $order->getPickUpLocationCity() . '</dd>';
                }
                if ($order->getPickUpLocationCountry()) {
                    $result .= '<dt>' . __('Custom Pick Up Location Country') . '</dt><dd>' . $order->getPickUpLocationCountry() . '</dd>';
                }
                $result .= '</dl>';

                $result .= '</div></section>';
            }
        }
        // $result .= '<pre>' . print_r($order->debug(), true) . '</pre>';
        return $result;
    }
}
