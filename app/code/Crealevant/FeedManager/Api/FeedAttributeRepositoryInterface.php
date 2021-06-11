<?php


namespace Crealevant\FeedManager\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface FeedAttributeRepositoryInterface
{


    /**
     * Save Feed_Atrribute
     * @param \Crealevant\FeedManager\Api\Data\FeedAttributeInterface $feedAttribute
     * @return \Crealevant\FeedManager\Api\Data\FeedAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Crealevant\FeedManager\Api\Data\FeedAttributeInterface $feedAttribute
    );

    /**
     * Retrieve Feed_Atrribute
     * @param string $feedAttributeId
     * @return \Crealevant\FeedManager\Api\Data\FeedAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($feedAttributeId);

    /**
     * Retrieve Feed_Atrribute matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Crealevant\FeedManager\Api\Data\FeedAttributeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Feed_Atrribute
     * @param \Crealevant\FeedManager\Api\Data\FeedAttributeInterface $feedAttribute
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Crealevant\FeedManager\Api\Data\FeedAttributeInterface $feedAttribute
    );

    /**
     * Delete Feed_Atrribute by ID
     * @param string $feedAttributeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($feedAttributeId);
}
