<?php
/**
 *
 * @package Lillik\PriceDecimal\Model\Plugin
 *
 * @author  Lilian Codreanu <lilian.codreanu@gmail.com>
 */


namespace Crealevant\Relevant\Model\PriceDecimal\Plugin;

use Crealevant\Relevant\Model\PriceDecimal\ConfigInterface;
use Crealevant\Relevant\Model\PriceDecimal\PricePrecisionConfigTrait;

abstract class PriceFormatPluginAbstract
{

    use PricePrecisionConfigTrait;

    /** @var ConfigInterface  */
    protected $moduleConfig;

    /**
     * @param \Lillik\PriceDecimal\Model\ConfigInterface $moduleConfig
     */
    public function __construct(
        ConfigInterface $moduleConfig
    ) {
        $this->moduleConfig  = $moduleConfig;
    }
}