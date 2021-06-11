<?php
namespace Crealevant\Relevant\Block;

class ThemeSettings extends \Magento\Framework\View\Element\Template
{
    protected $_scopeConfigObject;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product $product,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->_scopeConfigObject = $context->getScopeConfig();
        $this->_product = $product;
        parent::__construct($context, $data);
    }

    public function getSetting($group, $item) {
        return $this->_scopeConfigObject->getValue('settings/' . $group . '/'. $item, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function loadProduct($product)
    {
        return $this->_product->load($product);
    }
}
