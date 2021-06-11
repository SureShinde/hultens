<?php

namespace Crealevant\ProclientPyramidAPI\Plugin;

use Psr\Log\LoggerInterface;
use Proclient\PyramidAPI\Cron\GetArticles;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Indexer\Model\Indexer\CollectionFactory;

/**
 * Class GetArticlesPlugin
 * @package Crealevant\ProclientPyramidAPI\Plugin
 */
class GetArticlesPlugin
{
    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var CollectionFactory
     */
    protected $indexerCollectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * GetArticlesPlugin constructor.
     * @param IndexerFactory $indexerFactory
     * @param CollectionFactory $indexerCollectionFactory
     */
    public function __construct(
        LoggerInterface $logger,
        IndexerFactory $indexerFactory,
        CollectionFactory $indexerCollectionFactory
    ) {
        $this->logger = $logger;
        $this->indexerFactory = $indexerFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
    }

    /**
     * @param GetArticles $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(GetArticles $subject, $result)
    {
        $this->performReindex();
        return $result;
    }

    /**
     * Perform reindex process
     */
    private function performReindex()
    {
        $this->logger->info('Crealevant\ProclientPyramidAPI\Plugin\GetArticlesPlugin reindexed started');
        $indexer = $this->indexerFactory->create();
        $indexerCollection = $this->indexerCollectionFactory->create();
        $ids = $indexerCollection->getAllIds();

        foreach ($ids as $id) {
            $idx = $indexer->load($id);

            if ($idx->getStatus() != 'valid') {
                $this->logger->info('Crealevant\ProclientPyramidAPI\Plugin\GetArticlesPlugin reindexed: ' . $id);
                $idx->reindexRow($id);
            }
        }
        
        $this->logger->info('Crealevant\ProclientPyramidAPI\Plugin\GetArticlesPlugin reindexed completed');
    }
}