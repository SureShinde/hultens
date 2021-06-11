<?php

namespace Crealevant\FeedManager\Model\Product\Attribute;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options;

    protected $_attribute;

    protected $attrGroupCollection;

    protected $productAttrCollection;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $attrGroupCollection,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttrCollection
    )
    {
        $this->_attribute = $attribute;
        $this->attrGroupCollection = $attrGroupCollection;
        $this->productAttrCollection = $productAttrCollection;
    }


    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (null == $this->options) {
            $groupAttributesCollection = $this->productAttrCollection->create()->addVisibleFilter()->load();
            foreach ($groupAttributesCollection->getItems() as $attribute) {
                $attr = $this->_attribute->load($attribute->getAttributeId());
                $attributeCode = $attr->getAttributeCode();
                $this->options[] = [
                    'value' => $attributeCode,
                    'label' => $attributeCode
                ];
            }

			$this->options[] = ['value' => 'currency', 'label' => __('currency')];
			$this->options[] = ['value' => 'product_url', 'label' => __('product_url')];
			$this->options[] = ['value' => 'shipping_fee', 'label' => __('shipping_fee')];
			$this->options[] = ['value' => 'shipping_currency', 'label' => __('shipping_currency')];
			$this->options[] = ['value' => 'price_currency', 'label' => __('Price with currency')];
			$this->options[] = ['value' => 'special_price_currency', 'label' => __('Special price with currency')];
        }
        return $this->options;
    }
}