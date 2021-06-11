<?php


namespace Crealevant\FeedManager\Model\Feeds;

use Magento\Framework\App\Request\DataPersistorInterface;
use Crealevant\FeedManager\Model\ResourceModel\Feeds\CollectionFactory;
use Crealevant\FeedManager\Model\ResourceModel\FeedAttribute\CollectionFactory as FeedAttributeCollection;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $dataPersistor;

    protected $collection;

    protected $loadedData;

    protected $_feedAttributeCollection;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        FeedAttributeCollection $feedAttributeCollection,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->_feedAttributeCollection = $feedAttributeCollection;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
            $this->loadedData[$model->getId()]['attributes_container'] = $this->_feedAttributeCollection->create()->addFeedIdFilter($model->getData()['id'])->getData();
        }
        $data = $this->dataPersistor->get('crealevant_feeds');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->loadedData[$model->getId()]['attributes_container'] = $this->_feedAttributeCollection->create()->addFeedIdFilter($model->getData()['id'])->getData();
            $this->dataPersistor->clear('crealevant_feeds');
        }
        return $this->loadedData;
    }
}
