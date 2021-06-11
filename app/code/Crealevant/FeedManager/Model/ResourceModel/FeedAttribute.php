<?php


namespace Crealevant\FeedManager\Model\ResourceModel;

class FeedAttribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('crealevant_feeds_attribute', 'id');
    }
}
