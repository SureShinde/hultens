<?php


namespace Crealevant\FeedManager\Api\Data;

interface FeedAttributeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Feed_Atrribute list.
     * @return \Crealevant\FeedManager\Api\Data\FeedAttributeInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Crealevant\FeedManager\Api\Data\FeedAttributeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
