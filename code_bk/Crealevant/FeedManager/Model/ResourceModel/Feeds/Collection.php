<?php


namespace Crealevant\FeedManager\Model\ResourceModel\Feeds;

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
            'Crealevant\FeedManager\Model\Feeds',
            'Crealevant\FeedManager\Model\ResourceModel\Feeds'
        );
    }
}
