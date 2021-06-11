<?php

namespace Crealevant\CleanVarnishCache\Controller\Adminhtml\Varnishcache;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Crealevant\CleanVarnishCache\Helper\Data;
use Magento\Backend\App\Action\Context;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var Data
     */
    protected $helperCleanVarnishCache;

    public function __construct(
        Context $context,
        Data $helperCleanVarnishCache
    ) {
        $this->helperCleanVarnishCache = $helperCleanVarnishCache;
        parent::__construct($context);
    }

    public function execute()
    {
        $url = $this->getRequest()->getParam('url');
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            try {
                $this->helperCleanVarnishCache->runCurl($url);
                $this->messageManager->addSuccess(__('Clean cache successfully.'));
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Can\'t clean varnish cache.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/index', array('_current' => true));
            }
        } else {
            $this->messageManager->addError(__('Please enter a URL, example https://example.com'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', array('_current' => true));
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', array('_current' => true));
    }
}