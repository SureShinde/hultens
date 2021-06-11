<?php

namespace Crealevant\Lookbook\Block;

class Product extends \Crealevant\Lookbook\Block\AbstractLookbook
{
    /**
     * @var \Crealevant\Lookbook\Model\LookbookFactory
     */
    protected $lookbookFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Crealevant\Lookbook\Model\LookbookFactory $lookbookFactory,
        \Magento\Catalog\Block\Product\Context $productContext,
        \Crealevant\Lookbook\Helper\Data $_helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_productCollectionFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {
        parent::__construct($context, $productContext, $_helper, $_productCollectionFactory, $urlHelper, $data);
        $this->_coreRegistry = $productContext->getRegistry();
        $this->lookbookFactory = $lookbookFactory;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    public function getLookbook(){
        $product = $this->getProduct();
        $lookbookId = $product->getCrealevantLookbook();

        $lookbook = $this->lookbookFactory->create()->load($lookbookId);

        if($lookbook->getId()){
            return $lookbook;
        }
        return false;
    }
}
