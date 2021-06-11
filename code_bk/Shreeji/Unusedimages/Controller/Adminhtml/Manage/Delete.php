<?php

namespace Shreeji\Unusedimages\Controller\Adminhtml\Manage;

class Delete extends \Magento\Backend\App\Action {

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Shreeji_Unusedimages::unusedimages';

    /**
     *
     * @var \Magento\Catalog\Model\Product 
     */
    protected $_product;

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
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Catalog\Model\Product $product, \Magento\Framework\App\ResourceConnection $resource, \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig, \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->_product = $product;
        $this->_resource = $resource;
        $this->_connection = $this->_resource->getConnection();
        $this->_directoryList = $directoryList;
        $this->_catalogProductMediaConfig = $catalogProductMediaConfig;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute() {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('unusedimage_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Shreeji\Unusedimages\Model\Unusedimages')->load($id);
                $mediaPath = $this->_directoryList->getPath('media') . '/' . $this->_catalogProductMediaConfig->getBaseMediaPath();
                $filepath = $mediaPath . $model->getData('filename');
                if (file_exists($filepath)) {
                    unlink($filepath); // here we are deleting unused image
                    $model->delete();
                    // display success message
                    $this->messageManager->addSuccess(__('Image has been deleted.'));
                    return $resultRedirect->setPath('*/*/');
                } else {
                    $this->messageManager->addErrorMessage('Unable to delete image');
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/');
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a image to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

}
