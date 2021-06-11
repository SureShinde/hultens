<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Controller\Adminhtml\Shippingmethod;

/**
 *
 */
class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Mediastrategi\Unifaun\Model\ShippingmethodFactory
     */
    protected $_shippingmethodFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mediastrategi\Unifaun\Model\ShippingmethodFactory $resourceModel
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mediastrategi\Unifaun\Model\ShippingmethodFactory $resourceModel,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->_shippingmethodFactory = $resourceModel;
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * List shipping-methods here.
     * @return \Magento\Framework\Controller\Result\PageFactory
     */
    public function execute()
    {
        //Call page factory to render layout and page content
        $resultPage = $this->_resultPageFactory->create();

        //Set the menu which will be active for this page
        $resultPage->setActiveMenu('Mediastrategi_Unifaun::shippingmethod');

        //Set the header title of grid
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Methods'));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Mediastrategi'), __('Mediastrategi'));
        $resultPage->addBreadcrumb(__('Unifaun Online'), __('Shipping Methods'));

        return $resultPage;
    }

    /**
     * @internal
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mediastrategi_Unifaun::shippingmethod_index');
    }
}
