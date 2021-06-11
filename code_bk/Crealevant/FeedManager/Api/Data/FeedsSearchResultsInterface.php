<?php


namespace Crealevant\FeedManager\Api\Data;

interface FeedsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Feeds list.
     * @return \Crealevant\FeedManager\Api\Data\FeedsInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Crealevant\FeedManager\Api\Data\FeedsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
