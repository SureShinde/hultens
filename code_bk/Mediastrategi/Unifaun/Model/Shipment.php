<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Model;

/**
 *
 */
class Shipment extends \Magento\Framework\Model\AbstractModel
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mediastrategi\Unifaun\Model\ResourceModel\Shipment');
    }

    /**
     * @param string $shipmentId
     * @return array|bool
     */
    public function loadByShipmentId($shipmentId)
    {
        $id = $this->getResource()->loadByShipmentId($shipmentId);
        return $this->load($id);
    }

    /**
     * @param string $orderId
     * @return array|bool
     */
    public function loadByOrderId($orderId)
    {
        $id = $this->getResource()->loadByOrderId($orderId);
        return $this->load($id);
    }
}
