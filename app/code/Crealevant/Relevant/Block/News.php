<?php
namespace Crealevant\Relevant\Block;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;

class News extends ListProduct
{
    public function getLoadedProductCollection()
    {
        return $this->_productCollection;
    }

    public function setProductCollection(AbstractCollection $collection)
    {
        $this->_productCollection = $collection;
    }
}