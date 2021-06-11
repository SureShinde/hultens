<?php
/**
 *
 */

namespace Mediastrategi\Unifaun\Block\System\Config\Form\Field;

/**
 *
 */
class ParcelContents extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'label' => __('Product Categories'),
                'value' => 'categories',
            ),
            array(
                'label' => __('Product Names'),
                'value' => 'products',
            ),
            array(
                'label' => __('Empty'),
                'value' => 'empty',
            ),
        );
    }

}
