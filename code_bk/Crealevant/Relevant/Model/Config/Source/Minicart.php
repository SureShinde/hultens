<?php
namespace Crealevant\Relevant\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Minicart implements ArrayInterface
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
            '0' => __('Default'),
            '1' => __('Mega'),
            '2' => __('From right')
        ];
    }
}