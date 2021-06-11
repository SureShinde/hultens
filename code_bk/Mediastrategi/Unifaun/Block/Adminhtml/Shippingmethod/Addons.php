<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod;

/**
 *
 */
class Addons extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    /**
     *
     */
    public function toHtml()
    {
        $html = '';
        $value = $this->getValue();
        if (!is_array($value)) {
            $value = [];
        }
        $html .= '<script type="text/json" id="unifaun-addons-dynamic-fields">' . json_encode($this->getDynamicFields()) . '</script>';
        $html .= '<script type="text/json" id="unifaun-addons-options">' . json_encode($this->getOptions()) . '</script>';
        $html .= '<script type="text/json" id="unifaun-addons-values">' . json_encode($value) . '</script>';
        return $html;
    }
}
