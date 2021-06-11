<?php


namespace Crealevant\FeedManager\Controller\Adminhtml\Feeds;
use Crealevant\FeedManager\Model\FeedsFactory;
use Crealevant\FeedManager\Model\ResourceModel\FeedAttribute\CollectionFactory;

class Edit extends \Crealevant\FeedManager\Controller\Adminhtml\Feeds
{

    protected $resultPageFactory;

    protected $feedFactory;

    protected $feedAttributeCollection;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        FeedsFactory $feedFactory,
        CollectionFactory $feedAttrCollectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->feedFactory = $feedFactory;
        $this->feedAttributeCollection = $feedAttrCollectionFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $modelFeed = $this->feedFactory->create();
        $_collectionFeedAttr = $this->feedAttributeCollection->create();

        // 2. Initial checking
        if ($id) {
            $modelFeed->load($id);
            if (!$modelFeed->getId()) {
                $this->messageManager->addErrorMessage(__('This Feeds no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $_collectionFeedAttr->addFeedIdFilter($id);
        }
        $this->_coreRegistry->register('crealevant_feeds', $modelFeed);
        $this->_coreRegistry->register('crealevant_feeds_attribute', $_collectionFeedAttr);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Feed') : __('New Feed'),
            $id ? __('Edit Feed') : __('New Feed')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Feeds'));
        $resultPage->getConfig()->getTitle()->prepend($modelFeed->getId() ? $modelFeed->getName() : __('New Feed'));
        return $resultPage;
    }
}
