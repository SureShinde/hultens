<?php
namespace Crealevant\Relevant\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class MinicartMobile implements ArrayInterface
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
            '0' => __('No link'),
            '1' => __('From right')
        ];
    }
}