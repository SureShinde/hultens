<?php

namespace Crealevant\FeedManager\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportProductFeed extends Command {

    protected $executer;
    protected $state;

    public function __construct(
        \Crealevant\FeedManager\Model\Generate $executer,
        \Magento\Framework\App\State $state
    ) {
        $this->executer = $executer;
        $this->state = $state;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('crealevant:export_product_feed')->setDescription('Export product feed');
    }

    protected function execute(InputInterface $input, Outputinterface $output)
    {
        $this->state->setAreaCode('adminhtml');
        $this->executer->execute();

        echo "Done! \n";
    }
}