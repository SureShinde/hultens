<?php


namespace Crealevant\FeedManager\Controller\Adminhtml\Feeds;

class Delete extends \Crealevant\FeedManager\Controller\Adminhtml\Feeds
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Crealevant\FeedManager\Model\Feeds');
                $model->load($id);
                $model->delete();

                $collectionFeedAttr = $this->_objectManager->create('Crealevant\FeedManager\Model\ResourceModel\FeedAttribute\Collection');
                $collectionFeedAttr = $collectionFeedAttr->addFeedIdFilter($id);
                if ($collectionFeedAttr->getSize() > 0) {
                    /* Delete all attribute by feed id */
                    $collectionFeedAttr->walk('delete');
                }
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Feeds.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Feeds to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
