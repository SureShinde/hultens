<?php
/**
 *
 */

namespace Mediastrategi\Unifaun\Model\Cache;

/**
 *
 */
class Checkout extends \Magento\Framework\Cache\Frontend\Decorator\TagScope
{

    /**
     * @var string
     */
    const TYPE_IDENTIFIER = 'msunifaun_cache_checkout';

    /**
     * @var string
     */
    const CACHE_TAG = 'MSUNIFAUN_CACHE_CHECKOUT';

    /**
     * Cache expiration in seconds, set to 1 hour
     *
     * @var int
     */
    const CACHE_EXPIRATION = 60*60;

    /**
     * @param \Magento\Framework\App\Cache\Type\FrontendPool $cacheFrontendPool
     */
    public function __construct(\Magento\Framework\App\Cache\Type\FrontendPool $cacheFrontendPool)
    {
        parent::__construct(
            $cacheFrontendPool->get(self::TYPE_IDENTIFIER),
            self::CACHE_TAG
        );
    }
}
