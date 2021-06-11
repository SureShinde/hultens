<?php
/**
 *
 */

namespace Mediastrategi\Unifaun\Cron\Shippingmethod;

/**
 *
 */
class Automation
{

    /**
     * @internal
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @internal
     * @var \Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod\Automation
     */
    protected $_automationBlock;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     * @var \Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod\Automation $automationBlock
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod\Automation $automationBlock
    ) {
    
        $this->_logger = $logger;
        $this->_automationBlock = $automationBlock;
    }

    /**
     *
     */
    public function execute()
    {
        $this->_logger->info('Mediastrategi Unifaun Shipping Method Automation CRON started');

        try {
            $this->_automationBlock->execute();
            if ($log = $this->_automationBlock->getLog()) {
                $explodes = explode("\n", $log);
                if ($explodes) {
                    foreach ($explodes as $explode) {
                        $this->_logger->info($explode);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_logger->info('error: ' . $e->getMessage());
        }

        $this->_logger->info('Mediastrategi Unifaun Shipping Method Automation CRON ended');
    }
}
