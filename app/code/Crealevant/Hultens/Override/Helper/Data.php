<?php

namespace Crealevant\Hultens\Override\Helper;

class Data extends \Trustpilot\Reviews\Helper\Data
{
    public function loadCategoryProductInfo($scope, $scopeId) {
        try {
            $settings = json_decode(self::getConfig('master_settings_field', $scopeId, $scope));

            $skuSelector = $settings->skuSelector;
            $productList = $variationSkus = $variationIds = array();

            $params = $this->_request->getParams();
            $category = array_key_exists('id', $params) ? $params['id'] : $this->getFirstCategory($scopeId);
            $limit = array_key_exists('limit', $params) ? $params['limit'] : $this->scopeConfig->getValue('catalog/frontend/grid_per_page');
            /*override here*/
            $page = $params['p'] ?: 1;
            /*override here*/

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $layerResolver = $objectManager->get(\Magento\Catalog\Model\Layer\Resolver::class);
            $layer = $layerResolver->get();
            $layer->setCurrentCategory($category);

            $products = $layer->getProductCollection()->setPage($page, $limit);
            foreach ($products->getItems() as $product) {
                if ($product->getTypeId() == 'configurable') {
                    $childProducts = $this->_linkManagement->getChildren($product->getSku());
                    $variationSkus = $skuSelector  != 'id' ? $this->loadSelector($product, $skuSelector, $childProducts) : array();
                    $variationIds = $this->loadSelector($product, 'id', $childProducts);
                }
                $sku = $skuSelector != 'id' ? $this->loadSelector($product, $skuSelector) : '';
                $id = $this->loadSelector($product, 'id');
                array_push($productList, array(
                    "sku" => $sku,
                    "id" => $id,
                    "variationIds" => $variationIds,
                    "variationSkus" => $variationSkus,
                    "productUrl" => $product->getProductUrl() ?: '',
                    "name" => $product->getName(),
                ));
            }
            return $productList;
        } catch(\Throwable $e) {
            $description = 'Unable to load catagory product info ';
            $this->_trustpilotLog->error($e, $description, array(
                'scope' => $scope,
                'scopeId' => $scopeId
            ));
            return array();
        } catch(\Exception $e) {
            $description = 'Unable to load catagory product info ';
            $this->_trustpilotLog->error($e, $description, array(
                'scope' => $scope,
                'scopeId' => $scopeId
            ));
            return array();
        }
    }
}