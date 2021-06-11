<?php


namespace Crealevant\FeedManager\Model\ResourceModel\FeedAttribute;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Crealevant\FeedManager\Model\FeedAttribute',
            'Crealevant\FeedManager\Model\ResourceModel\FeedAttribute'
        );
    }

    /**
     * @param $feedId
     * @return $this
     */
    public function addFeedIdFilter($feedId)
    {
        $this->getSelect()->where(
            'id_feed = ?',
            $feedId
        );

        return $this;
    }
}
