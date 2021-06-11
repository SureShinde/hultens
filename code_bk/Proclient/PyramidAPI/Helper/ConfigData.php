<?php

namespace Proclient\PyramidAPI\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Store\Model\ScopeInterface;

class ConfigData extends AbstractHelper {

	const XML_PATH_PYRAMIDAPI = 'pyramidapi/';

	public function getConfigValue($field, $storeId = null) {
		return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
	}

	public function getGeneralConfig($code, $storeId = null) {
		return $this->getConfigValue(self::XML_PATH_PYRAMIDAPI .'general/'. $code, $storeId);
	}

  public function getAuthConfig($code, $storeId = null) {
		return $this->getConfigValue(self::XML_PATH_PYRAMIDAPI .'auth/'. $code, $storeId);
	}

  public function getCustomersConfig($code, $storeId = null) {
		return $this->getConfigValue(self::XML_PATH_PYRAMIDAPI .'customers/'. $code, $storeId);
	}

  public function getOrdersConfig($code, $storeId = null) {
		return $this->getConfigValue(self::XML_PATH_PYRAMIDAPI .'orders/'. $code, $storeId);
	}

  public function getArticlesConfig($code, $storeId = null) {
		return $this->getConfigValue(self::XML_PATH_PYRAMIDAPI .'articles/'. $code, $storeId);
	}
}
