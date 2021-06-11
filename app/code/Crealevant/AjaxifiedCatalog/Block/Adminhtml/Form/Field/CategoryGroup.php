<?php

namespace Crealevant\AjaxifiedCatalog\Block\Adminhtml\Form\Field;


class CategoryGroup extends \Magento\Framework\View\Element\Html\Select {

    protected $_categoryModal;
    /**
     * methodList
     *
     * @var array
     */

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Catalog\Model\Category $categoryModal,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_categoryModal = $categoryModal;
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
        $categoryGroupCollection = $this->_categoryModal->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('is_active', 1);
        $productCategory = [];
        foreach ($categoryGroupCollection as $categoryGroup) {
            $productCategory[] = [ 'value' => $categoryGroup->getName(), 'label' => $categoryGroup->getName() ];
        }
        return $productCategory;
    }
}