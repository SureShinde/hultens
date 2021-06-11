<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Crealevant\Lookbook\Controller\Adminhtml\Lookbook;

use Magento\Backend\App\Action;

class Delete extends \Crealevant\Lookbook\Controller\Adminhtml\Lookbook
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
		$resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
		if ($id) {
            try {
                $model = $this->_objectManager->create('Crealevant\Lookbook\Model\Lookbook');
                $model->setId($id);
                $model->load($id);
				$title =  $model->getName();
				$model->delete();
				$this->messageManager->addSuccess(__('You deleted the lookbook "%1".', $title));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
