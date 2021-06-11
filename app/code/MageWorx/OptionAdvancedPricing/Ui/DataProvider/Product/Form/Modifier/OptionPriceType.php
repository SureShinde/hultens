<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionAdvancedPricing\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use MageWorx\OptionBase\Ui\DataProvider\Product\Form\Modifier\ModifierInterface;

class OptionPriceType extends AbstractModifier implements ModifierInterface
{
    /**
     * Get sort order of modifier to load modifiers in the right order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return 150;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->updatePriceTypeFieldConfig();

        return $this->meta;
    }

    /**
     * update Price Type Field Config
     */
    protected function updatePriceTypeFieldConfig()
    {
        $this->meta[CustomOptions::GROUP_CUSTOM_OPTIONS_NAME]['children'][CustomOptions::GRID_OPTIONS_NAME]
        ['children']['record']['children'][CustomOptions::CONTAINER_OPTION]['children']
        [ CustomOptions::CONTAINER_TYPE_STATIC_NAME]['children'][CustomOptions::FIELD_PRICE_TYPE_NAME]
        ['arguments']['data']['config']['component'] =
            'MageWorx_OptionAdvancedPricing/component/custom-options-price-type';

        $this->meta[CustomOptions::GROUP_CUSTOM_OPTIONS_NAME]['children'][CustomOptions::GRID_OPTIONS_NAME]
        ['children']['record']['children'][CustomOptions::CONTAINER_OPTION]['children']
        [ CustomOptions::GRID_TYPE_SELECT_NAME]['children']['record']['children'][CustomOptions::FIELD_PRICE_TYPE_NAME]
        ['arguments']['data']['config']['component'] =
            'MageWorx_OptionAdvancedPricing/component/custom-options-price-type';
    }

    /**
     * Check is current modifier for the product only
     *
     * @return bool
     */
    public function isProductScopeOnly()
    {
        return false;
    }
}
