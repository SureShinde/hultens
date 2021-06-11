<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_DemoModule
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Crealevant\Relevant\Model\Config\Source;

/**
 * Used in creating options for getting product type value
 *
 */
class FooterPayment
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'visa', 'label'=>__('Visa')],
            ['value' => 'mastercard', 'label'=>__('Mastercard')],
            ['value' => 'klarna', 'label'=>__('Klarna')],
            ['value' => 'credit-card', 'label'=>__('DIBS')]
        ];
    }
}