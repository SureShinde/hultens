<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Model\ResourceModel\Shipment;

/**
 *
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Mediastrategi\Unifaun\Model\Shipment',
            'Mediastrategi\Unifaun\Model\ResourceModel\Shipment'
        );
    }
}
