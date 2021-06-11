<?php


namespace Crealevant\FeedManager\Model;

use Crealevant\FeedManager\Api\Data\FeedAttributeInterface;

class FeedAttribute extends \Magento\Framework\Model\AbstractModel implements FeedAttributeInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Crealevant\FeedManager\Model\ResourceModel\FeedAttribute');
    }

    /**
     * Get id
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param string $id
     * @return \Crealevant\FeedManager\Api\Data\FeedAtrributeInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get id_feed
     * @return string
     */
    public function getIdFeed()
    {
        return $this->getData(self::ID_FEED);
    }

    /**
     * Set id_feed
     * @param string $idFeed
     * @return \Crealevant\FeedManager\Api\Data\FeedAtrributeInterface
     */
    public function setIdFeed($idFeed)
    {
        return $this->setData(self::ID_FEED, $idFeed);
    }

    /**
     * Get attribute_value
     * @return string
     */
    public function getAttributeValue()
    {
        return $this->getData(self::ATTRIBUTE_VALUE);
    }

    /**
     * Set attribute_value
     * @param string $attributeValue
     * @return \Crealevant\FeedManager\Api\Data\FeedAtrributeInterface
     */
    public function setAttributeValue($attributeValue)
    {
        return $this->setData(self::ATTRIBUTE_VALUE, $attributeValue);
    }
}
