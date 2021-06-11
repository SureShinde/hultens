<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Ui\Component\Listing\Column;

/**
 *
 */
class OrderTracking extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @var \Mediastrategi\Unifaun\Model\ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    private $backendHelper;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $components
     * @param array $data
     * @param \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Backend\Helper\Data $backendHelper,
        array $components = [],
        array $data = [],
        \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory
    ) {
        $this->backendHelper = $backendHelper;
        $this->shipmentFactory = $shipmentFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @todo Add stuff here
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!empty($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item)
            {
                if (!empty($item['entity_id'])) {
                    $shipmentModel = $this->shipmentFactory->create();
                    $shipment = $shipmentModel->loadByOrderId($item['entity_id']);
                    if (!empty($shipment->getData())) {
                        $item['msunifaun_tracking_url'] = sprintf(
                            '<a onclick="(event.stopPropagation() || event.cancelBubble);" '
                            . 'target="_blank" href="%s">%s</a>',
                            $this->backendHelper->getUrl(
                                'msunifaun/shipment/track',
                                ['id' => $shipment->getData('shipment_id')]
                            ),
                            __('Track')
                        );
                    }
                }
            }
        }
        return $dataSource;
    }
}
