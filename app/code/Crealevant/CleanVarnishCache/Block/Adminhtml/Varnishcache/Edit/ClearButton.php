<?php
namespace Crealevant\CleanVarnishCache\Block\Adminhtml\Varnishcache\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ClearButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Slide'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}