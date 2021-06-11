<?php
/**
 *
 */
namespace Crealevant\Unifaun\Plugin;

/**
 *
 */
class ShippingRatePlugin
{

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data
     */
    private $helper;

    /**
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     */
    public function __construct(
        \Mediastrategi\Unifaun\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Rate $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult $rate
     * @return $this
     */
    public function aroundImportShippingRate($subject, $proceed, $rate)
    {
        if ($this->helper->isEnabled()) {
            $result = $proceed($rate);
            $result->setData(
                'msunifaun_agents',
                $rate->getData('msunifaun_agents')
            );
            $result->setData(
                'msunifaun_carrier',
                $rate->getData('msunifaun_carrier')
            );
            $result->setData(
                'msunifaun_description',
                $rate->getData('msunifaun_description')
            );
            $result->setData(
                'msunifaun_image',
                $rate->getData('msunifaun_image')
            );
            return $result;
        } else {
            return $proceed($rate);
        }
    }
}
