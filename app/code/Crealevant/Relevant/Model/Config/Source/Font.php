<?php
namespace Crealevant\Relevant\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Font implements ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];

        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return [
            'arial' => __('Arial'),
            'Playfair Display' => __('Playfair Display'),
            'Lato' => __('Lato'),
            'myriad-pro' => __('Myriad Pro'),
            'Helvetica Neue' => __('Helvetica Neue'),
            'Abril Fatface' => __('Abril Fatface'),
            'Roboto' => __('Roboto'),
            'Pacifico' => __('Pacifico'),
            'Open Sans Condensed' => __('Open Sans Condensed'),
            'Oswald' => __('Oswald')

        ];
    }
}