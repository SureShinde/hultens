<?php
/**
 *
 */

namespace Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod;

/**
 *
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mediastrategi_Unifaun';
        $this->_controller = 'unifaun_shippingmethod';
        $this->_headerText = __('Manage Shipping Methods');
        $this->_addButtonLabel = __('Add New Shipping Method');
        parent::_construct();
    }
}
