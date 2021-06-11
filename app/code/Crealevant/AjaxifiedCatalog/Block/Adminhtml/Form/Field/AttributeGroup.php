<?php

namespace Crealevant\AjaxifiedCatalog\Block\Adminhtml\Form\Field;


class AttributeGroup extends \Magento\Framework\View\Element\Html\Select {

    protected $_attributeFactory;
    /**
     * methodList
     *
     * @var array
     */

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_attributeFactory = $attributeFactory;
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }
    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml() {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        $this->setExtraParams('multiple="multiple"');
        return parent::_toHtml();
    }
    private function getSourceOptions()
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
            $attributeOptions[$i]['value'] = $attribute['frontend_label'];
            $i++;
        }
        return $attributeOptions;
    }
}