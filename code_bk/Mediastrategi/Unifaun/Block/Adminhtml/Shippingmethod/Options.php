<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod;

/**
 *
 */
class Options extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    /**
     *
     */
    public function toHtml()
    {
        $html = '<div class="shippingmethod_options admin__field field field-options" style="margin-left: 0;">';

        $html .= '<div class="table-wrapper"><table><thead><tr>';
        $html .= '<th class="title">' . __('Title') . '</th>';
        $html .= '<th>' . __('Country') . '</th>';
        $html .= '<th>' . __('Zip') . '</th>';
        $html .= '<th>' . __('Weight') . '</th>';
        $html .= '<th>' . __('Width') . '</th>';
        $html .= '<th>' . __('Height') . '</th>';
        $html .= '<th>' . __('Depth') . '</th>';
        $html .= '<th>' . __('Volume') . '</th>';
        $html .= '<th>' . __('Cart Subtotal') . '</th>';
        $html .= '<th class="price">' . __('Price') . '</th>';
        $html .= '<th>&nbsp;</th></tr></thead><tbody>';

        $items = $this->getData('value');
        if (empty($items)) {
            $items = [
                [
                    'title' => __('Everywhere'),
                    'country' => '*',
                    'zip' => '*',
                    'weight' => '*',
                    'width' => '*',
                    'height' => '*',
                    'depth' => '*',
                    'volume' => '*',
                    'cart_subtotal' => '*',
                    'price' => 250,
                ]
            ];
        }

        $i = 0;
        foreach ($items as $item) {
            $html .= '<tr>';
            $html .= '<td class="title"><input type="text" name="options[' . $i . '][title]" value="' . $item['title'] . '" /></td>';
            $html .= '<td><input type="text" name="options[' . $i . '][country]" value="' . $item['country'] . '" /></td>';
            $html .= '<td><input type="text" name="options[' . $i . '][zip]" value="' . $item['zip'] . '" /></td>';
            $html .= '<td><input type="text" name="options[' . $i . '][weight]" value="' . $item['weight'] . '" /></td>';
            $html .= '<td><input type="text" name="options[' . $i . '][width]" value="' . $item['width'] . '" /></td>';
            $html .= '<td><input type="text" name="options[' . $i . '][height]" value="' . $item['height'] . '" /></td>';
            $html .= '<td><input type="text" name="options[' . $i . '][depth]" value="' . $item['depth'] . '" /></td>';
            $html .= '<td><input type="text" name="options[' . $i . '][volume]" value="' . $item['volume'] . '" /></td>';
            $html .= '<td><input type="text" name="options[' . $i . '][cart_subtotal]" value="' . $item['cart_subtotal'] . '" /></td>';
            $html .= '<td class="price"><input type="text" name="options[' . $i . '][price]" value="' . $item['price'] . '" /></td>';
            $html .= '<td><button class="secondary"><span>-</span></button></td>';
            $html .= '</tr>';
            $i++;
        }

        $html .= '</tbody></table></div>';
        $html .= '<div class="buttons"><button class="action-default primary"><span>' . __('+ Add row') . '</span></button></div>';
        $html .= '</div>';
        return $html;
    }
}
