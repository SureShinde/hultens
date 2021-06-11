<?php
/**
 *
 */

namespace Mediastrategi\Unifaun\Block\System\Config\Form\Field;

/**
 *
 */
class ProductAttribute extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
*/
    private $attributeCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
* @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory)
    {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        static $options;
        if (!isset($options)) {
            $options = [[
                'label' => __('None'),
                'value' => '',
            ]];
            $collection = $this->attributeCollectionFactory->create();
            foreach ($collection as $item)
            {
                $attributeCode = $item->getAttributeCode();
                if (!empty($attributeCode)) {
                    $options[] = [
                        'label' => $attributeCode,
                        'value' => $attributeCode
                    ];
                }
            }
        }
        return $options;
    }

}
