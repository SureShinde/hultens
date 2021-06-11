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
class Add extends \Magento\Backend\App\Action
{

    /**
     * @var \Mediastrategi\Unifaun\Model\ShippingmethodFactory
     */
    protected $_shippingmethodFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingMethodFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Result\PageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingMethodFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->_shippingmethodFactory = $shippingMethodFactory;
        $this->_resultPageFactory = $pageFactory;
        $this->_authSession = $authSession;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $title = (string) $this->getRequest()->getParam('title');
        $store = (int) $this->getRequest()->getParam('store');
        $method = (string) $this->getRequest()->getParam('method');
        $active = (bool) $this->getRequest()->getParam('active');
        $storedShipments = (bool) $this->getRequest()->getParam('stored_shipment');
        $pickup = (string) $this->getRequest()->getParam('pickup');
        $automationEnable = (bool) $this->getRequest()->getParam('automation_enable');
        $automationPackageType = (string) $this->getRequest()->getParam('automation_package_type');
        $automationOrderStatusBefore = (string) $this->getRequest()->getParam('automation_order_status_before');
        $adminUsername = $this->_authSession->getUser()->getUserName();
        $options = (array) $this->getRequest()->getParam('options');
        $extra = ($this->getRequest()->getParam('extra') && json_decode($this->getRequest()->getParam('extra'), true)
                  ? json_decode($this->getRequest()->getParam('extra'), true)
                  : []);
        $extraValid = empty($this->getRequest()->getParam('extra'))
                    || (json_decode($this->getRequest()->getParam('extra'), true) !== null);
        $allAddons = (array) $this->getRequest()->getParam('addons');
        $addons = (isset($allAddons[$method]) ? (array) $allAddons[$method] : []);
        $customsEnabled = (bool) $this->getRequest()->getParam('customs_enabled');
        $customsDocuments = (array) $this->getRequest()->getParam('customs_documents');

        if (!empty($title)
            && !empty($method)
            && $extraValid
        ) {
            // Remove empty rows from data
            foreach ($options as $key => $row) {
                $isEmpty = true;
                foreach ($row as $value) {
                    if (!empty($value)) {
                        $isEmpty = false;
                        break;
                    }
                }
                if ($isEmpty) {
                    unset($options[$key]);
                }
            }

            // Add specifications data
            $allAddonsSpecifications = $this->getRequest()->getParam('addons_specifications');
            foreach (array_keys($addons) as $addon) {
                if (!empty($allAddonsSpecifications[$method][$addon])) {
                    $addons[$addon] = (array) $allAddonsSpecifications[$method][$addon];
                }
            }

            /** @var \Magento\Eav\Model\Entity\AbstractEntity $model */
            $shippingMethod = $this->_shippingmethodFactory->create();
            $shippingMethod->setData('store', $store);
            $shippingMethod->setData('title', $title);
            $shippingMethod->setData('method', $method);
            $shippingMethod->setData('active', $active);
            $shippingMethod->setData('stored_shipment', $storedShipments);
            $shippingMethod->setData('pickup', $pickup);
            $shippingMethod->setData('automation_enable', $automationEnable);
            $shippingMethod->setData('automation_package_type', $automationPackageType);
            $shippingMethod->setData('automation_order_status_before', $automationOrderStatusBefore);
            $shippingMethod->setData('automation_admin_username', $adminUsername);
            $shippingMethod->setData('options', json_encode($options));
            $shippingMethod->setData('extra', json_encode($extra));
            $shippingMethod->setData('addons', json_encode($addons));
            $shippingMethod->setData('customs_enabled', $customsEnabled);
            $shippingMethod->setData('customs_documents', json_encode($customsDocuments));

            try {
                $shippingMethod->save();
                $this->messageManager->addSuccess(__('You added the shipping method.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath(
                'msunifaun/shippingmethod/index'
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mediastrategi_Unifaun::shippingmethod_add');
        $resultPage->getConfig()->getTitle()->prepend(__('Add Shipping Method'));
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
        return $this->_authorization->isAllowed('Mediastrategi_Unifaun::shippingmethod_add');
    }
}
