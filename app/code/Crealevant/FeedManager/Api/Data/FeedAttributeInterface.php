<?php


namespace Crealevant\FeedManager\Api\Data;

interface FeedAttributeInterface
{

    const ID_FEED = 'id_feed';
    const ID = 'id';
    const ATTRIBUTE_VALUE = 'attribute_value';
    const FEED_ATRRIBUTE_ID = 'feed_atrribute_id';


    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Crealevant\FeedManager\Api\Data\FeedAttributeInterface
     */
    public function setId($id);

    /**
     * Get id_feed
     * @return string|null
     */
    public function getIdFeed();

    /**
     * Set id_feed
     * @param string $idFeed
     * @return \Crealevant\FeedManager\Api\Data\FeedAttributeInterface
     */
    public function setIdFeed($idFeed);

    /**
     * Get attribute_value
     * @return string|null
     */
    public function getAttributeValue();

    /**
     * Set attribute_value
     * @param string $attributeValue
     * @return \Crealevant\FeedManager\Api\Data\FeedAttributeInterface
     */
    public function setAttributeValue($attributeValue);
}
