<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Plugin\Order;

/**
 *
 */
class Get
{

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data $helper
     */
    private $helper;
    /**
     * @var \Mediastrategi\Unifaun\Model\Order\UnifaunAssignmentFactory
     */
    private $unifaunAssignmentFactory;
    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     * @param \Mediastrategi\Unifaun\Model\Order\UnifaunAssignmentFactory $unifaunAssignmentFactory
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        \Mediastrategi\Unifaun\Helper\Data $helper,
        \Mediastrategi\Unifaun\Model\Order\UnifaunAssignmentFactory $unifaunAssignmentFactory,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->helper = $helper;
        $this->unifaunAssignmentFactory = $unifaunAssignmentFactory;
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $resultOrder
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $resultOrder
    )
    {
        $resultOrder = $this->getUnifaunAssignments($resultOrder);

        return $resultOrder;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $resultOrder
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Model\ResourceModel\Order\Collection $resultOrder
    )
    {
        foreach ($resultOrder->getItems() as $order) {
            $this->afterGet($subject, $order);
        }

        return $resultOrder;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    private function getUnifaunAssignments(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $pick_up_location_id = $order->getPickUpLocationId();
        $pick_up_location_name = $order->getPickUpLocationName();
        $pick_up_location_address = $order->getPickUpLocationAddress();
        $pick_up_location_zip_code = $order->getPickUpLocationZipCode();
        $pick_up_location_city = $order->getPickUpLocationCity();
        $pick_up_location_country = $order->getPickUpLocationCountry();

        $extensionAttributes = $order->getExtensionAttributes();
        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();

        $unifaunAssignments = $this->unifaunAssignmentFactory->create();
        $unifaunAssignments->setPickUpLocationId($pick_up_location_id);
        $unifaunAssignments->setPickUpLocationName($pick_up_location_name);
        $unifaunAssignments->setPickUpLocationAddress($pick_up_location_address);
        $unifaunAssignments->setPickUpLocationZipCode($pick_up_location_zip_code);
        $unifaunAssignments->setPickUpLocationCity($pick_up_location_city);
        $unifaunAssignments->setPickUpLocationCountry($pick_up_location_country);

        $orderExtension->setUnifaunAssignments($unifaunAssignments);
        $order->setExtensionAttributes($orderExtension);

        return $order;
    }

}
