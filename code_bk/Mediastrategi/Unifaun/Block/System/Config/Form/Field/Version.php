<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Block\System\Config\Form\Field;

/**
 *
 */
class Version extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var string
     */
    const EXTENSION_URL = 'http://www.mediastrategi.se/';

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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_helper->getExtensionVersion();
    }
}
