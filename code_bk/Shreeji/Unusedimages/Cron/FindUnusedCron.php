<?php

namespace Shreeji\Unusedimages\Cron;

use Magento\Store\Model\ScopeInterface;

class FindUnusedCron {

    const XML_PATH_UNUSED_ENABLED = 'unusedimage/general/enabled';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     *
     * @var \Shreeji\Unusedimages\Model\FindUnused 
     */
    protected $_findUnused;

    public function __construct(
    \Psr\Log\LoggerInterface $logger, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Shreeji\Unusedimages\Model\FindUnused $findUnused
    ) {
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_findUnused = $findUnused;
    }

    /**
     * Find unused images
     *
     * @return $this
     */
    public function execute() {
        if (!$this->_scopeConfig->isSetFlag(self::XML_PATH_UNUSED_ENABLED, ScopeInterface::SCOPE_STORE)) {
            return $this;
        }
        $this->_errors = [];
        try {
            $this->_findUnused->findUnusedImages();
            $message = "Unused images has been successfully find by CRON job";
            $this->_logger->info($message);
        } catch (\Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
            $this->_logger->info($e->getMessage());
            $this->_logger->critical($e);
        }

        return $this;
    }

}
