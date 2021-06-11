<?php
namespace Crealevant\FeedManager\Cron;

class ExportProductFeed
{
    protected $_generate;

    public function __construct(
        \Crealevant\FeedManager\Model\Generate $generate
    )
    {
        $this->_generate = $generate;
    }

    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Run Cron ---- ".date("Y-m-d H:i:s"));
        $this->_generate->execute();
    }
}