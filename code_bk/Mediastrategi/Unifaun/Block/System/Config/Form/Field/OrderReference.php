<?php
/**
 *
 */

namespace Mediastrategi\Unifaun\Block\System\Config\Form\Field;

/**
 *
 */
class OrderReference extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Order Id'),
                'value' => 'id',
            ],
            [
                'label' => __('Order Number'),
                'value' => 'number',
            ],
        ];
    }

}
