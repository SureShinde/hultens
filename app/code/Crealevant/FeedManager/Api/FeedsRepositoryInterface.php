<?php


namespace Crealevant\FeedManager\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface FeedsRepositoryInterface
{


    /**
     * Save Feeds
     * @param \Crealevant\FeedManager\Api\Data\FeedsInterface $feeds
     * @return \Crealevant\FeedManager\Api\Data\FeedsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Crealevant\FeedManager\Api\Data\FeedsInterface $feeds
    );

    /**
     * Retrieve Feeds
     * @param string $feedsId
     * @return \Crealevant\FeedManager\Api\Data\FeedsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($feedsId);

    /**
     * Retrieve Feeds matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Crealevant\FeedManager\Api\Data\FeedsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Feeds
     * @param \Crealevant\FeedManager\Api\Data\FeedsInterface $feeds
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Crealevant\FeedManager\Api\Data\FeedsInterface $feeds
    );

    /**
     * Delete Feeds by ID
     * @param string $feedsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($feedsId);
}
