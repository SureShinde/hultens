<?php

namespace Crealevant\AjaxifiedCatalog\Model\Config\Source;

class Select implements \Magento\Framework\Option\ArrayInterface
{
    protected $_categoryModal;

    public function __construct(
        \Magento\Catalog\Model\Category $categoryModal )
    {
        $this->_categoryModal = $categoryModal;
    }
    /**
     *   Options getter
     *   _____________________________________
     *   @return array
     */
    public function toOptionArray()
    {
        $productCategorys = $this->_categoryModal->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('is_active', 1);
        $productCategory = [];
        foreach ($productCategorys as $category) {
            $productCategory[] = [ 'value' => $category->getId(), 'label' => $category->getName() ];
        }
        return $productCategory;
    }

    /*
    *   Get options in "key-value" format
    *   _____________________________________
    *   @return array
    */
    public function toArray()
    {
        return [
            'label' => $this->__("category"),
            'class' => 'required-entry',
            'required' => 'true',
            'name' => 'cat_id',
            'values' => $this->toOptionArray()
        ];
    }
}