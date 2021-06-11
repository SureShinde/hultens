<?php
namespace Crealevant\Hultens\Override\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Trustpilot\Reviews\Helper\Data;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ObjectManager;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;

class Trustbox extends \Trustpilot\Reviews\Block\Trustbox
{
    protected $_helper;
    protected $_registry;
    protected $_request;
    protected $_storeManager;
    protected $_urlInterface;
    protected $_linkManagement;

    public function __construct(
        Context $context,
        Data $helper,
        Registry $registry,
        Http $request,
        LinkManagementInterface $linkManagement,
        array $data = [])
    {
        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_request = $request;
        $this->_storeManager = $context->getStoreManager();
        $this->_urlInterface = ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
        $this->_linkManagement = $linkManagement;
        parent::__construct($context, $helper, $registry, $request, $linkManagement, $data);
    }

    private function getCurrentUrl() {
        return $this->_urlInterface->getCurrentUrl();
    }

    public function loadTrustboxes()
    {
        $scope = $this->_helper->getScope();
        $storeId = $this->_helper->getWebsiteOrStoreId();
        $settings = json_decode($this->_helper->getConfig('master_settings_field', $storeId, $scope));
        $trustboxSettings = $settings->trustbox;
        if (isset($trustboxSettings->trustboxes)) {
            $currentUrl = $this->getCurrentUrl();
            $loadedTrustboxes = $this->loadPageTrustboxes($settings, $currentUrl);

            if ($this->_registry->registry('current_product')) {
                $loadedTrustboxes = array_merge((array)$this->loadPageTrustboxes($settings, 'product'), (array)$loadedTrustboxes);
            }
            else if ($this->_registry->registry('current_category')) {
                $loadedTrustboxes = array_merge((array)$this->loadPageTrustboxes($settings, 'category'), (array)$loadedTrustboxes);
                /*override here*/
                //$trustboxSettings->categoryProductsData = $this->_helper->loadCategoryProductInfo($scope, $storeId);
                /*override here*/
            }
            if ($this->_request->getFullActionName() == 'cms_index_index') {
                $loadedTrustboxes = array_merge((array)$this->loadPageTrustboxes($settings, 'landing'), (array)$loadedTrustboxes);
            }

            if (count($loadedTrustboxes) > 0) {
                $trustboxSettings->trustboxes = $loadedTrustboxes;
                return json_encode($trustboxSettings, JSON_HEX_APOS);
            }
        }

        return '{"trustboxes":[]}';
    }

    private function loadPageTrustboxes($settings, $page)
    {
        $data = [];
        $skuSelector = empty($settings->skuSelector) || $settings->skuSelector == 'none' ? 'sku' : $settings->skuSelector;
        foreach ($settings->trustbox->trustboxes as $trustbox) {
            if ((rtrim($trustbox->page, '/') == rtrim($page, '/') || $this->checkCustomPage($trustbox->page, $page)) && $trustbox->enabled == 'enabled') {
                $current_product = $this->_registry->registry('current_product');
                if ($current_product) {
                    $skus = array();
                    $productSku = $this->_helper->loadSelector($current_product, $skuSelector);
                    if ($productSku) {
                        array_push($skus, $productSku);
                    }
                    array_push($skus, \Trustpilot\Reviews\Model\Config::TRUSTPILOT_PRODUCT_ID_PREFIX . $current_product->getId());
                    if ($current_product->getTypeId() == 'configurable') {
                        $collection = $this->_linkManagement->getChildren($current_product->getSku());
                        foreach ($collection as $product) {
                            $productSku = $this->_helper->loadSelector($product, $skuSelector);
                            if ($productSku) {
                                array_push($skus, $productSku);
                            }
                            array_push($skus, \Trustpilot\Reviews\Model\Config::TRUSTPILOT_PRODUCT_ID_PREFIX . $product->getId());
                        }
                    }
                    $trustbox->sku = implode(',', $skus);
                    $trustbox->name = $current_product->getName();
                }
                array_push($data, $trustbox);
            }
        }
        return $data;
    }

    private function checkCustomPage($tbPage, $page) {
        return (
            $tbPage == strtolower(base64_encode($page . '/')) ||
            $tbPage == strtolower(base64_encode($page)) ||
            $tbPage == strtolower(base64_encode(rtrim($page, '/')))
        );
    }
}