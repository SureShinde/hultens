<?php
/**
 *
 * @package Lillik\PriceDecimal\Model
 *
 * @author  Lilian Codreanu <lilian.codreanu@gmail.com>
 */


namespace Crealevant\Relevant\Model\PriceDecimal;

interface ConfigInterface
{
    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig();

    /**
     * @return mixed
     */
    public function isEnable();
}