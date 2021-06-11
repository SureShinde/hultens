<?php
/**
 * Copyright © 2017 x-mage2(Crealevant). All rights reserved.
 * See README.md for details.
 */
namespace Crealevant\ProductSlider\Block\Product\Widget;

use Magento\Catalog\Block\Product\Context;

/**
 * Class NewWidget
 * @package Crealevant\ProductSlider\Block\Product\Widget
 */
class NewWidget extends AbstractWidget
{
    protected function getDetailsRendererList()
    {
        $this->setWidgetName('crealevant_new_product_slider');
        return parent::getDetailsRendererList();
    }
}