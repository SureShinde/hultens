<?php
namespace Mediastrategi\Unifaun\Model\Shippingmethod\Config\Structure;

class Data extends \Magento\Config\Model\Config\Structure\Data
{

    /**
     *
     */
    public function __construct(
        \Mediastrategi\Unifaun\Model\Shippingmethod\Config\Structure\Reader $reader,
        \Magento\Framework\Config\ScopeInterface $configScope,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId
    ) {
        parent::__construct(
            $reader,
            $configScope,
            $cache,
            $cacheId
        );
    }
}
