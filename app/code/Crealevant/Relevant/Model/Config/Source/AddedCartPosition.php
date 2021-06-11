<?php
namespace Crealevant\Relevant\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class AddedCartPosition implements ArrayInterface
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
            'under' => __('Under the shopping cart'),
            'bottom' => __('Fixed bottom')
        ];
    }
}