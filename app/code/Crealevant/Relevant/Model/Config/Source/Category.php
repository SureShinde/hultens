<?php

namespace Crealevant\Relevant\Model\Config\Source;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Option\ArrayInterface;

class Category implements ArrayInterface
{

    private $_categoryFactory;

    public function __construct(CategoryFactory $categoryFactory)
    {
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        /** @var \Magento\Catalog\Model\Category $item */
        foreach ($this->_categoryFactory->create()->getCollection()->getItems() as $item) {
            $category = $this->_categoryFactory->create()->load($item->getId());
            $options[] = array("value" => $category->getId(), "label" => $category->getName());
        }

        return $options;
    }
}
