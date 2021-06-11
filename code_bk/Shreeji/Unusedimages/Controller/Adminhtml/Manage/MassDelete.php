<?php

namespace Shreeji\Unusedimages\Controller\Adminhtml\Manage;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Shreeji\Unusedimages\Model\ResourceModel\Unusedimages\CollectionFactory;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action {

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     *
     * @var \Magento\Framework\App\ResourceConnection 
     */
    protected $_resource;

    /**
     *
     * @var connection 
     */
    protected $_connection;

    /**
     * Catalog product media config
     *
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $_catalogProductMediaConfig;

    /**
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList  
     */
    protected $_directoryList;

    /**
     * 
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory    
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, \Magento\Framework\App\ResourceConnection $resource, \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig, \Magento\Framework\App\Filesystem\DirectoryList $directoryList) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_resource = $resource;
        $this->_connection = $this->_resource->getConnection();
        $this->_directoryList = $directoryList;
        $this->_catalogProductMediaConfig = $catalogProductMediaConfig;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute() {
        $this->collectionFactory->create()->getData();
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $mediaPath = $this->_directoryList->getPath('media') . '/' . $this->_catalogProductMediaConfig->getBaseMediaPath();
        foreach ($collection as $image) {            
            $filepath = $mediaPath . $image->getData('filename');
            if (file_exists($filepath)) {
                unlink($filepath); // here we are deleting unused image
            }
            $image->delete();
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

}
