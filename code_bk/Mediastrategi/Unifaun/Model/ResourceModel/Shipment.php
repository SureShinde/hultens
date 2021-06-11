<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Model\ResourceModel;

/**
 *
 */
class Shipment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            'ms_unifaun_shipment',
            'id'
        );
    }

    /**
     * @param string $shipmentId
     * @return int
     */
    public function loadByShipmentId($shipmentId)
    {
        $table = $this->getMainTable();
        $where = $this->getConnection()->quoteInto("shipment_id = ?", $shipmentId);
        $sql = $this->getConnection()->select()->from($table, ['id'])->where($where);
        $id = $this->getConnection()->fetchOne($sql);
        return $id;
    }

    /**
     * @param string $orderId
     * @return int
     */
    public function loadByOrderId($orderId)
    {
        $table = $this->getMainTable();
        $where = $this->getConnection()->quoteInto("order_id = ?", $orderId);
        $sql = $this->getConnection()->select()->from($table, ['id'])->where($where);
        $id = $this->getConnection()->fetchOne($sql);
        return $id;
    }
}
