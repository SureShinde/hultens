<?php

namespace Crealevant\CleanVarnishCache\Block\Adminhtml\Grid\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid_record');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Action'));
    }
}