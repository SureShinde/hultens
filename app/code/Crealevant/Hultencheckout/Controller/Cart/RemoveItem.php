<?php

namespace Crealevant\Hultencheckout\Controller\Cart;

class RemoveItem extends \Magento\Framework\App\Action\Action
{

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->cart = $cart;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Delete shopping cart item action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $results['success'] = 1;
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $results['success'] = 0;
            return $this->resultJsonFactory->create()->setData($results);
        }

        $id = (int)$this->getRequest()->getParam('item_id');
        if ($id) {
            try {
                $this->cart->removeItem($id)->save();
            } catch (\Exception $e) {
                $results['success'] = 0;
                $results['message'] = $this->messageManager->addError(__('We can\'t remove the item.'));
            }
        }
        return $this->resultJsonFactory->create()->setData($results);
    }
}
