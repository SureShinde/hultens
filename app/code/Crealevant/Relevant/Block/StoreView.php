<?php
namespace Crealevant\Relevant\Block;

class StoreView extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $data = []
    ) {
        $this->_postDataHelper = $postDataHelper;
        parent::__construct($context, $data);
    }

    public function getStores()
    {
        if (!$this->getData('stores')) {
            $rawStores = $this->getRawStores();

            $groupId = $this->getCurrentGroupId();
            if (!isset($rawStores[$groupId])) {
                $stores = [];
            } else {
                $stores = $rawStores[$groupId];
            }
            $this->setData('stores', $stores);
        }
        return $this->getData('stores');
    }

    public function getOtherStoreUrl()
    {
        foreach ($this->_storeManager->getStores() as $store) {
            if ($store->getId() != $this->_storeManager->getStore()->getId()) {
                return $this->_storeManager->getStore($store->getId())->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
            }
        }
    }
}
