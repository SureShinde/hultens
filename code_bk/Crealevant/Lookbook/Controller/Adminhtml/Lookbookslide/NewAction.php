<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Crealevant\Lookbook\Controller\Adminhtml\Lookbookslide;

class NewAction extends \Crealevant\Lookbook\Controller\Adminhtml\Lookbookslide
{
    /**
     * Create new customer action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
}
