<?php
namespace Crealevant\Relevant\Controller\Index;

use Crealevant\Relevant\Block\News;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /** @var PageFactory */
    protected $pageFactory;

    /** @var  \Magento\Catalog\Model\ResourceModel\Product\Collection */
    protected $productCollection;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
    )
    {
        $this->pageFactory = $pageFactory;
        $this->productCollection = $collectionFactory->create();
        $this->_catalogProductVisibility = $catalogProductVisibility;

        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->pageFactory->create();
        $result->getConfig()->getTitle()->set('Nyheter - ');

        // obtain product collection.
        $this->productCollection->addFieldToSelect('*');
        $this->productCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $todayDate = date('Y-m-d');
        $this->productCollection->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ])->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        );

        // get the custom list block and add our collection to it
        /** @var News $list */
        $result->getLayout()->getBlock('custom.products.list')->setProductCollection($this->productCollection);

        return $result;
    }
}