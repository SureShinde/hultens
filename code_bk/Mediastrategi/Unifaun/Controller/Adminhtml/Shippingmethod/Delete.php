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
class Delete extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Mediastrategi\Unifaun\Model\ShippingmethodFactory
     */
    protected $_shippingmethodFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mediastrategi\Unifaun\Model\ShippingmethodFactory $resourceModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mediastrategi\Unifaun\Model\ShippingmethodFactory $resourceModel
    ) {
    
        parent::__construct($context);
        $this->_shippingmethodFactory = $resourceModel;
    }

    /**
     * @return \Magento\Backend\Mode\View\Result\Forward
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        /** @var \Magento\Eav\Model\Entity\AbstractEntity $model */
        $model = $this->_shippingmethodFactory->create();
        $shippingMethod = $model->load($id);

        try {
            if (!empty($shippingMethod)
                && $model->delete($shippingMethod)
            ) {
                $this->messageManager->addSuccess(__('You deleted the shipping method.'));
            } else {
                $this->messageManager->addError(__('Failed to delete shipping method'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath(
            'msunifaun/shippingmethod/index'
        );
    }

    /**
     * @internal
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mediastrategi_Unifaun::shippingmethod_delete');
    }
}
