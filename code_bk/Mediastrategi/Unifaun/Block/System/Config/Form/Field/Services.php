<?php
/**
 *
 */

namespace Mediastrategi\Unifaun\Block\System\Config\Form\Field;

/**
 *
 */
class Services extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data $helper
     */
    protected $_helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mediastrategi\Unifaun\Helper\Data $helper
    ) {
        $this->_helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $carrierServices = $this->_helper->getCarrierServices();
        $optionArray = [];
        foreach ($carrierServices as $carrierId => $options) {
            foreach ($options['services'] as $serviceCode => $serviceLabel) {
                $optionArray[] = [
                    'label' => $serviceLabel,
                    'value' => $carrierId . '_' . $serviceCode,
                ];
            }
        }
        return $optionArray;
    }

}
