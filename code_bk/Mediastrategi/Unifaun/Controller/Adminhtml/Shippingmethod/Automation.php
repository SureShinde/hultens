<?php
/**
 *
 */

/**
 *
 */
namespace Mediastrategi\Unifaun\Controller\Adminhtml\Shippingmethod;

/**
 *
 */
class Automation extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
    
        parent::__construct($context);
        $this->_resultPageFactory = $pageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mediastrategi_Unifaun::shippingmethod_automation');
        $resultPage->getConfig()->getTitle()->prepend(__('Run Automation'));
        $resultPage->addBreadcrumb(
            __('Shipping Methods'),
            __('Shipping Methods')
        );
        return $resultPage;
    }

    /**
     * @internal
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mediastrategi_Unifaun::shippingmethod_automation');
    }
}
