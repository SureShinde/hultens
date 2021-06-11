<?php


namespace Crealevant\FeedManager\Model;

use Magento\Framework\Api\DataObjectHelper;
use Crealevant\FeedManager\Api\Data\FeedAttributeSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Crealevant\FeedManager\Api\Data\FeedAttributeInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Crealevant\FeedManager\Model\ResourceModel\FeedAttribute\CollectionFactory as FeedAttributeCollectionFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Crealevant\FeedManager\Model\ResourceModel\FeedAttribute as ResourceFeedAttribute;
use Crealevant\FeedManager\Api\FeedAttributeRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

class FeedAttributeRepository implements feedAttributeRepositoryInterface
{

    protected $dataObjectProcessor;

    protected $dataFeedAttributeFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $resource;

    protected $feedAttributeFactory;

    protected $searchResultsFactory;

    protected $feedAttributeCollectionFactory;


    /**
     * @param ResourceFeedAttribute $resource
     * @param FeedAttributeFactory $feedAttributeFactory
     * @param FeedAttributeInterfaceFactory $dataFeedAttributeFactory
     * @param FeedAttributeCollectionFactory $feedAttributeCollectionFactory
     * @param FeedAttributeSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceFeedAttribute $resource,
        FeedAttributeFactory $feedAttributeFactory,
        FeedAttributeInterfaceFactory $dataFeedAttributeFactory,
        FeedAttributeCollectionFactory $feedAttributeCollectionFactory,
        FeedAttributeSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->feedAttributeFactory = $feedAttributeFactory;
        $this->feedAttributeCollectionFactory = $feedAttributeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFeedAttributeFactory = $dataFeedAttributeFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Crealevant\FeedManager\Api\Data\FeedAttributeInterface $feedAttribute
    ) {
        /* if (empty($feedAtrribute->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $feedAtrribute->setStoreId($storeId);
        } */
        try {
            $feedAttribute->getResource()->save($feedAttribute);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the feedAtrribute: %1',
                $exception->getMessage()
            ));
        }
        return $feedAttribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($feedAttributeId)
    {
        $feedAttribute = $this->feedAtrributeFactory->create();
		$feedAttribute->getResource()->load($feedAttribute, $feedAttributeId);
        if (!$feedAttribute->getId()) {
            throw new NoSuchEntityException(__('Feed_Atrribute with id "%1" does not exist.', $feedAttributeId));
        }
        return $feedAttribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->feedAtrributeCollectionFactory->create();
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
        \Crealevant\FeedManager\Api\Data\FeedAttributeInterface $feedAttribute
    ) {
        try {
			$feedAttribute->getResource()->delete($feedAttribute);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Feed_Attribute: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($feedAttributeId)
    {
        return $this->delete($this->getById($feedAttributeId));
    }
}
