<?php


namespace Crealevant\FeedManager\Controller\Adminhtml\Feeds;

use Magento\Framework\Exception\LocalizedException;
use Crealevant\FeedManager\Model\FeedsFactory;
use Crealevant\FeedManager\Model\FeedAttributeFactory;
use Crealevant\FeedManager\Model\ResourceModel\FeedAttribute\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    protected $feedFactory;

    protected $feedAttributeFactory;

    protected $feedAttributeCollection;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        FeedsFactory $feedFactory,
        FeedAttributeFactory $feedAttributeFactory,
        CollectionFactory $feedAttrCollectionFactory
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->feedFactory = $feedFactory;
        $this->feedAttributeFactory = $feedAttributeFactory;
        $this->feedAttributeCollection = $feedAttrCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {

            $id = $this->getRequest()->getParam('id');

            $modelFeed = $this->feedFactory->create()->load($id);
            if (!$modelFeed->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Feed no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $modelFeed->setData($data);

            try {
                $modelFeed->save();
                if (isset($data['attributes_container']) && $attributeFeedData = $data['attributes_container']) {
                    if ($modelFeed->getId()) {
                        if ($id) {
                            $_collectionFeedAttr = $this->feedAttributeCollection->create()->addFeedIdFilter($id);

                            if ($_collectionFeedAttr->getSize() > 0) {
                                $_collectionFeedAttr->walk('delete');
                            }
                        }

                        foreach ($attributeFeedData as $attributeFeed) {
                            $modelFeedAttribute = $this->feedAttributeFactory->create();
                            $modelFeedAttribute->setTitle($attributeFeed['title']);
                            $modelFeedAttribute->setAttributeValue($attributeFeed['attribute_value']);
                            $modelFeedAttribute->setIdFeed($modelFeed->getId());
                            try {
                                $modelFeedAttribute->save();
                            } catch (LocalizedException $e) {
                                $this->messageManager->addErrorMessage($e->getMessage());
                            } catch (\Exception $e) {
                                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Feed Attribute.'));
                            }
                        }
                    }
                } else {
                    if ($id) {
                        $_collectionFeedAttr = $this->feedAttributeCollection->create()->addFeedIdFilter($id);
                        if ($_collectionFeedAttr->getSize() > 0) {
                            /* Delete all attribute by feed id */
                            $_collectionFeedAttr->walk('delete');
                        }
                    }
                }
                $this->messageManager->addSuccessMessage(__('You saved the Feed.'));
                $this->dataPersistor->clear('crealevant_feeds');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $modelFeed->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Feeds.'));
            }

            $this->dataPersistor->set('crealevant_feeds', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
