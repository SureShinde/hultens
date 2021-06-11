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
class MassEnable extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Mediastrategi\Unifaun\Model\ShippingmethodFactory
     */
    protected $_shippingmethodFactory;

    /**
     * @var \Mediastrategi\Unifaun\Model\ResourceModel\Shippingmethod\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mediastrategi\Unifaun\Model\ShippingmethodFactory $resourceModel
     * @param \Mediastrategi\Unifaun\Model\ResourceModel\Shippingmethod\CollectionFactory $collectionFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mediastrategi\Unifaun\Model\ShippingmethodFactory $resourceModel,
        \Mediastrategi\Unifaun\Model\ResourceModel\Shippingmethod\CollectionFactory $collectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
    
        parent::__construct($context);
        $this->_shippingmethodFactory = $resourceModel;
        $this->_collectionFactory = $collectionFactory;
        $this->_filter = $filter;
    }

    /**
     * @return \Magento\Backend\Mode\View\Result\Forward
     */
    public function execute()
    {
        $model = $this->_shippingmethodFactory->create();
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $updated = 0;
        foreach ($collection as $item) {
            $shippingMethod = $model->load($item['id']);
            if (!empty($shippingMethod->getData())) {
                $shippingMethod->setData('active', true);
                try {
                    $shippingMethod->save();
                    $updated++;
                } catch (\Exception $e) {
                }
            }
        }
        if ($updated) {
            $this->messageManager->addSuccess(__('Enabled the shipping methods.'));
        } else {
            $this->messageManager->addError(__('Failed to enable the shipping methods'));
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
        return $this->_authorization->isAllowed('Mediastrategi_Unifaun::shippingmethod_massEnable');
    }
}
