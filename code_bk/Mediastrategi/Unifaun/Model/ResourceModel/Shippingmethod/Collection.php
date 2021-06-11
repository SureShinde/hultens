<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Model\ResourceModel\Shippingmethod;

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
            'Mediastrategi\Unifaun\Model\Shippingmethod',
            'Mediastrategi\Unifaun\Model\ResourceModel\Shippingmethod'
        );
    }
}
