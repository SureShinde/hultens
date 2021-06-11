<?php declare(strict_types=1);


namespace Crealevant\CleanVarnishCache\Block\Adminhtml\Varnishcache;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Container;

class Index extends Container
{
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }


    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Crealevant_CleanVarnishCache';
        $this->_controller = 'adminhtml_grid';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Clean'));
    }

    public function getHeaderText()
    {
        return __('Clean Vanish Cache');
    }
}
