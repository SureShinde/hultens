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
class Track extends \Magento\Framework\App\Action\Action
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
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory,
        \Mediastrategi\Unifaun\Helper\Data $helper
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $shipmentId = (string) $this->getRequest()->getParam('id');
        if ($shipmentId) {
            $shipmentModel = $this->shipmentFactory->create();
            $shipment = $shipmentModel->loadByShipmentId($shipmentId);
            if (!empty($shipment->getData())) {
                $trackingUrl = $this->helper->getShipmentTrackingLink(
                    $shipment,
                    $this->helper->getStoreConfig(
                        'carriers/msunifaun/credentials/user_id'
                    )
                );
                return $resultRedirect->setPath(
                    $trackingUrl
                );
            }
        }
        $this->messageManager->addError(__(
            'Failed to find tracking for shipment %1',
            $shipmentId
        ));
        return $resultRedirect->setPath('sales/order/index');
    }
}
