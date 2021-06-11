<?php


namespace Crealevant\FeedManager\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Crealevant\FeedManager\Api\Data\FeedsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Crealevant\FeedManager\Api\Data\FeedsSearchResultsInterfaceFactory;
use Crealevant\FeedManager\Model\ResourceModel\Feeds\CollectionFactory as FeedsCollectionFactory;
use Crealevant\FeedManager\Api\FeedsRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Crealevant\FeedManager\Model\ResourceModel\Feeds as ResourceFeeds;
use Magento\Framework\Exception\CouldNotDeleteException;

class FeedsRepository implements feedsRepositoryInterface
{

    protected $dataObjectProcessor;

    private $storeManager;

    protected $dataObjectHelper;

    protected $resource;

    protected $feedsCollectionFactory;

    protected $feedsFactory;

    protected $searchResultsFactory;

    protected $dataFeedsFactory;


    /**
     * @param ResourceFeeds $resource
     * @param FeedsFactory $feedsFactory
     * @param FeedsInterfaceFactory $dataFeedsFactory
     * @param FeedsCollectionFactory $feedsCollectionFactory
     * @param FeedsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceFeeds $resource,
        FeedsFactory $feedsFactory,
        FeedsInterfaceFactory $dataFeedsFactory,
        FeedsCollectionFactory $feedsCollectionFactory,
        FeedsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->feedsFactory = $feedsFactory;
        $this->feedsCollectionFactory = $feedsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFeedsFactory = $dataFeedsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Crealevant\FeedManager\Api\Data\FeedsInterface $feeds
    ) {
        /* if (empty($feeds->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $feeds->setStoreId($storeId);
        } */
        try {
            $feeds->getResource()->save($feeds);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the feeds: %1',
                $exception->getMessage()
            ));
        }
        return $feeds;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($feedsId)
    {
        $feeds = $this->feedsFactory->create();
        $feeds->getResource()->load($feeds, $feedsId);
        if (!$feeds->getId()) {
            throw new NoSuchEntityException(__('Feeds with id "%1" does not exist.', $feedsId));
        }
        return $feeds;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->feedsCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Crealevant\FeedManager\Api\Data\FeedsInterface $feeds
    ) {
        try {
            $feeds->getResource()->delete($feeds);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Feeds: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($feedsId)
    {
        return $this->delete($this->getById($feedsId));
    }
}
