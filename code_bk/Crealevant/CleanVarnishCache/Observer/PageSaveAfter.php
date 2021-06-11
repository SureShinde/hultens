<?php

namespace Crealevant\CleanVarnishCache\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Crealevant\CleanVarnishCache\Helper\Data;
use Magento\Cms\Helper\Page;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Cms\Model\PageFactory;

class PageSaveAfter implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helperCleanVarnishCache;

    /**
     * @var Page
     */
    protected $helperPage;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * PageSaveAfter constructor.
     * @param StoreManagerInterface $storeManager
     * @param Data $helperCleanVarnishCache
     * @param Page $helperPage
     * @param UrlInterface $urlBuilder
     * @param PageFactory $pageFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Data $helperCleanVarnishCache,
        Page $helperPage,
        UrlInterface $urlBuilder,
        PageFactory $pageFactory
    ) {
        $this->storeManager = $storeManager;
        $this->helperCleanVarnishCache = $helperCleanVarnishCache;
        $this->helperPage = $helperPage;
        $this->urlBuilder = $urlBuilder;
        $this->pageFactory = $pageFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $cmsPage = $observer->getEvent()->getObject();
        $url = $this->helperPage->getPageUrl($cmsPage->getId());
        $this->helperCleanVarnishCache->runCurl($url);
    }
}
