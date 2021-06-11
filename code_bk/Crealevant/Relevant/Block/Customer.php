<?php
/**
 * Catalog super product configurable part block
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Crealevant\Relevant\Block;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Customer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;


    public function __construct(
                 \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\App\Request\Http $request,
                                \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
                                \Magento\Customer\Model\SessionFactory $customerSession
    )
    {
        $this->request = $request;
        $this->customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        $this->_urlBuilder = $context->getUrlBuilder();

        parent::__construct($context);
    }

    public function getCustomer()
    {
        $customer = $this->_customerSession->create()->getCustomer();
        return $customer;
    }

    /**
     * @return string
     */
    public function getChangePasswordUrl()
    {
        return $this->_urlBuilder->getUrl('customer/account/edit/changepass/1');
    }


    public function isLoggedIn()
    {
        if ($this->_customerSession->create()->isLoggedIn()) {
            return true;
        } else {
            return false;
        }
    }
}