<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod;

/**
 *
 */
class Extra extends \Magento\Framework\Data\Form\Element\AbstractElement
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
        $html .= '<span>' . __('Specify optional additional settings for shipment-body in JSON format below.') . '</span>';
        $html .= '<br /><textarea name="extra">' . json_encode($value) . '</textarea>';
        if ($this->getError()) {
            $html .= '<p>Please enter a valid JSON.</p>';
        }
        return $html;
    }
}
