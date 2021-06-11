<?php
/**
 *
 */

/**
 *
 */
namespace Mediastrategi\Unifaun\Controller\Adminhtml\Shipment;

/**
 *
 */
class Label extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Mediastrategi\Unifaun\Model\ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $orderModel;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     * @param \Magento\Sales\Model\Order $orderModel
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory,
        \Mediastrategi\Unifaun\Helper\Data $helper,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->helper = $helper;
        $this->orderModel = $orderModel;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $orderId = (int) $this->getRequest()->getParam('id');
        if ($order = $this->orderModel->load($orderId)) {
            if ($shipmentCollection = $order->getShipmentsCollection()) {
                $shipmentId = false;
                foreach ($shipmentCollection as $shipment) {
                    if (!empty($shipment['entity_id'])
                        && !empty($shipment['shipping_label'])
                    ) {
                        return $this->fileFactory->create(
                            'Shipping-Label-' . $orderId . '-' . $shipment['entity_id'] . '.pdf',
                            $shipment['shipping_label'],
                            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
                    }
                }
                if ($shipmentId !== false) {
                    return $resultRedirect->setPath(
                        'admin/order_shipment/printLabel',
                        [
                            'shipment_id' => $shipmentId
                        ]
                    );
                }
            }
        }

        $this->messageManager->addError(sprintf(
            __('Failed to find label for shipment %d'),
            $orderId
        ));
        return $resultRedirect->setPath('sales/order/index');
    }
}
