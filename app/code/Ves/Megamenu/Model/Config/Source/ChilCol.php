<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Megamenu\Model\Config\Source;
use Magento\Framework\Data\OptionSourceInterface;

class ChilCol implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
                'label' => '1',
                'value' => 1,
            ];
        $options[] = [
                'label' => '2',
                'value' => 2,
            ];
        $options[] = [
                'label' => '3',
                'value' => 3,
            ];
        $options[] = [
                'label' => '4',
                'value' => 4,
            ];
        $options[] = [
                'label' => '5',
                'value' => 5,
            ];
        $options[] = [
                'label' => '6',
                'value' => 6,
            ];
        $options[] = [
                'label' => '7',
                'value' => 7,
            ];
        $options[] = [
                'label' => '8',
                'value' => 8,
            ];
        $options[] = [
                'label' => '9',
                'value' => 9,
            ];
        $options[] = [
                'label' => '10',
                'value' => 10,
            ];
        $options[] = [
                'label' => '11',
                'value' => 11,
            ];
        $options[] = [
                'label' => '12',
                'value' => 12,
            ];
        return $options;
    }
}
