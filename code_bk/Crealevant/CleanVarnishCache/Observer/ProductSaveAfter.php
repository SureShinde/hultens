<?php

namespace Crealevant\CleanVarnishCache\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;
use Crealevant\CleanVarnishCache\Helper\Data;

class ProductSaveAfter implements ObserverInterface
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Data
     */
    protected $helperCleanVarnishCache;

    /**
     * @var Http
     */
    protected $request;

    /**
     * ProductSaveAfter constructor.
     * @param StoreManagerInterface $storeManager
     * @param Http $request
     * @param Data $helperCleanVarnishCache
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Http $request,
        Data $helperCleanVarnishCache
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->helperCleanVarnishCache = $helperCleanVarnishCache;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        $storeId = $this->request->getParam('store');
        $url = $this->storeManager->getStore($storeId)->getBaseUrl() . $product->getUrlKey();
        $this->helperCleanVarnishCache->runCurl($url);
    }
}
