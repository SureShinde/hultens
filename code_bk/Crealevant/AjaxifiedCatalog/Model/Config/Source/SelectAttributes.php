<?php

namespace Crealevant\AjaxifiedCatalog\Model\Config\Source;

class SelectAttributes implements \Magento\Framework\Option\ArrayInterface
{
    protected $_attributeFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory

    ){
        $this->_attributeFactory = $attributeFactory;
    }

    /**
     *   Options getter
     *   _____________________________________
     *   @return array
     */
    public function toOptionArray()
    {
        $attribute_data = [];
        $attributeInfo = $this->_attributeFactory->create();
        foreach ($attributeInfo as $items) {
            $attribute_data[] = $items->getData();
        }

        $attributeOptions = [];
        $i = 0;
        foreach ($attribute_data as $attribute) {
            $attributeOptions[$i]['label'] = $attribute['frontend_label'];
            $attributeOptions[$i]['value'] = $attribute['attribute_id'];
            $i++;
        }
        return $attributeOptions;
    }


    /*
    *   Get options in "key-value" format
    *   _____________________________________
    *   @return array
    */
    public function toArray()
    {
        return [
            'label' => $this->__("Attribute"),
            'class' => 'required-entry',
            'required' => 'true',
            'name' => 'attribute_id',
            'values' => $this->toOptionArray()
        ];
    }
}