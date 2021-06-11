<?php
/**
 * Created by PhpStorm.
 * User: henrikj
 * Date: 16/11/16
 * Time: 13:17
 */

namespace Crealevant\Relevant\Plugin;

use Magento\Catalog\Helper\Product\View;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\View\Result\Page;
use Magento\Quote\Model\Quote;

/**
 * Class Plugin
 * @package Crealevant\Simplecheckout\Plugin
 */
class LayoutPerAttributeSet
{
//    protected $quoteRepository;
//
//    public function __construct(
//        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
//    ) {
//        $this->quoteRepository = $quoteRepository;
//    }

    /**
     *
     * @param View $view
     * @param Page $resultPage
     * @param $product
     * @param null $params
     * @internal param $subject
     * @internal param $cartId
     * @internal param PaymentInterface $method
     */
    public function beforeInitProductLayout(
        View $view,
        Page $resultPage,
        $product,
        $params = null
    ) {
        if ($this->_isOfAttributeSet($product, 'ticket')) {
            if (!$params) {
                $params = new DataObject();
            }
            $afterHandles = $params->getAfterHandles();
            if (!$afterHandles) {
                $afterHandles = array();
            }
            $afterHandles[] = 'catalog_product_view_ticket';
            $params->setAfterHandles($afterHandles);
        }
        return [$resultPage, $product, $params];
    }

    /**
     * @param Product $product
     * @param string $attributeSetName
     * @return bool
     */
    private function _isOfAttributeSet(Product $product, $attributeSetName)
    {
        $objectManager = ObjectManager::getInstance();
        $attributeSet = $objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
        $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
        return strcmp($attributeSetRepository->getAttributeSetName(), $attributeSetName) == 0;
    }
}