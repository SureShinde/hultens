<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Model\ResourceModel;

/**
 *
 */
class Shippingmethod extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            'ms_unifaun_shippingmethod',
            'id'
        );
    }
}
