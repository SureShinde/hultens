<?php

namespace Crealevant\FeedManager\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreRepository;

class Store implements ArrayInterface
{
    /**
     * @var Rate
     */
    protected $_storeRepository;

    /**
     * @param StoreRepository $storeRepository
     */
    public function __construct(
        StoreRepository $storeRepository
    ) {
        $this->_storeRepository = $storeRepository;
    }

    public function toOptionArray()
    {
        $stores = $this->_storeRepository->getList();
        $storeList = [];

        foreach ($stores as $store) {
            $storeId = $store["store_id"];
            $storeName = $store["name"];

            // skip store Admin
            if ($storeId == 0) {
                continue;
            }
            $storeList[] = [
                'value' => $storeId,
                'label' => $storeName
            ];
        }

        return $storeList;
    }
}