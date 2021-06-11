<?php

namespace Crealevant\AjaxifiedCatalog\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;

/**
 * ArraySerialized helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION = 'ajaxfied_catalog/filter_settings/popular_items_position';

    protected $_storeManager;
    protected $serialize;
    protected $_catalogData;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Magento\Catalog\Helper\Data $catalogData)
    {
        $this->_storeManager = $storeManager;
        $this->_catalogData = $catalogData;
        $this->serialize = $serialize;

        parent::__construct($context);
    }

    public function getConfigValue($configPath, $store = null)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get serialized config value
     * temporarily solution to get unserialized config value
     * should be deprecated in 2.3.x
     *
     * @param $configPath
     * @param null $store
     * @return mixed
     */
    public function getSerializedConfigValue($configPath, $store = null)
    {
        $value = $this->getConfigValue($configPath, $store);

        if (empty($value)) return false;

        if ($this->isSerialized($value)) {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Unserialize\Unserialize::class);
        } else {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
        }

        return $unserializer->unserialize($value);
    }
    public function getStoreid()
    {
        return $this->_storeManager->getStore()->getId();
    }
    /**
     * Get Data from the column attribute group
     **/
    public function getColumnAttributeGroup(){
        $attributeGroupConfig = $this->scopeConfig->getValue(self::XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION,ScopeInterface::SCOPE_STORE,$this->getStoreid());

        if($attributeGroupConfig == '' || $attributeGroupConfig == null)
            return;

        $unserializedata = $this->serialize->unserialize($attributeGroupConfig);

        $attributeGroupArray = array();
        foreach($unserializedata as $key => $row)
        {
            $attributeGroupArray[] = $row['attribute_group'];
        }

        return $attributeGroupArray;
    }
    /**
     * Get data from the column category group
     **/
    public function getColumnCategoryGroup() {
        $categoryGroupConfig = $this->scopeConfig->getValue(self::XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION,ScopeInterface::SCOPE_STORE,$this->getStoreid());

        if($categoryGroupConfig == '' || $categoryGroupConfig == null)
            return;

        $unserializedata = $this->serialize->unserialize($categoryGroupConfig);

        $categoryGroupArray = array();
        foreach($unserializedata as $key => $row)
        {
            $categoryGroupArray[] = $row['category_group'];
        }

        return $categoryGroupArray;
    }
    /**
     * Get data from the column menu position
     **/
    public function getColumnMenuPosition() {
        $menuPositionConfig = $this->scopeConfig->getValue(self::XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION,ScopeInterface::SCOPE_STORE,$this->getStoreid());

        if($menuPositionConfig == '' || $menuPositionConfig == null)
            return;

        $unserializedata = $this->serialize->unserialize($menuPositionConfig);

        $menuPositionArray = array();
        foreach($unserializedata as $key => $row)
        {
            $menuPositionArray[] = $row['menu_position'];
        }

        return $menuPositionArray;
    }
    /**
     * Get data from the column menu position
     **/
    public function getColumnPosition() {
        $columnConfig = $this->scopeConfig->getValue(self::XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION,ScopeInterface::SCOPE_STORE,$this->getStoreid());
        $catalogHelperData = $this->_catalogData;

        if($columnConfig == '' || $columnConfig == null)
            return;

        $unserializedata = $this->serialize->unserialize($columnConfig);
        $categoryGroup = $this->getColumnCategoryGroup();
        $columnArray = array();
        if($catalogHelperData->getCategory()){
            $currentCategoryName = $catalogHelperData->getCategory()->getName();
            if(in_array($currentCategoryName, $categoryGroup)){
                foreach($unserializedata as $key => $row) {
                    $columnArray[] = $row['attribute_group'];
                }
            }
        }
        return $columnArray;
    }
    public function popularFilterPosition() {
        $popularConfig = $this->scopeConfig->getValue(self::XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION,ScopeInterface::SCOPE_STORE,$this->getStoreid());
        $catalogHelperData = $this->_catalogData;

        if($popularConfig == '' || $popularConfig == null)
            return;

        $unserializedata = $this->serialize->unserialize($popularConfig);
        $columnCategory = $this->getColumnCategoryGroup();
        $columnArray = array();
        if($catalogHelperData->getCategory()){
            $currentCategoryName = $catalogHelperData->getCategory()->getName();
            if(in_array($currentCategoryName, $columnCategory)){
                foreach($unserializedata as $key => $row) {
                    $columnArray[] = $row['attribute_group'];
                }
            }
        }
        return $columnArray;
    }
    public function isFilterPosition() {
        $isConfig = $this->scopeConfig->getValue(self::XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION,ScopeInterface::SCOPE_STORE,$this->getStoreid());
        $catalogHelperData = $this->_catalogData;

        if($isConfig == '' || $isConfig == null)
            return;

        $unserializedata = $this->serialize->unserialize($isConfig);
        $columnCategory = $this->getColumnCategoryGroup();
        $columnsArray = array();
        if($catalogHelperData->getCategory()){
            $currentCategoryName = $catalogHelperData->getCategory()->getName();
            if(in_array($currentCategoryName, $columnCategory)){
                foreach($unserializedata as $key => $row) {
                    $rowNum = $row;
                    $attrName = $row['menu_position'];
                    if(in_array($attrName, $rowNum)){
                        $columnsArray[] = $attrName . $row['attribute_group'] . $row['menu_position'];
                    }
                }
            }
        }
        return $columnsArray;
    }
    public function getPopularFilterRow() {
        $filterRow = $this->scopeConfig->getValue(self::XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION,ScopeInterface::SCOPE_STORE,$this->getStoreid());
        $catalogHelperData = $this->_catalogData;

        if($filterRow == '' || $filterRow == null)
            return;

        $unserializedata = $this->serialize->unserialize($filterRow);
        $columnCategory = $this->getColumnCategoryGroup();
        $columnsArray = array();
        if($catalogHelperData->getCategory()){
            $currentCategoryName = $catalogHelperData->getCategory()->getName();
            if(in_array($currentCategoryName, $columnCategory)){
                foreach($unserializedata as $key => $row) {
                    $rowNum = $row;
                    $attrName = $row['menu_position'];

                        $columnsArray[] = $rowNum;
                    }
            }
        }
        return $columnsArray;
    }
    /**
     * Check if value is a serialized string
     *
     * @param string $value
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }

}