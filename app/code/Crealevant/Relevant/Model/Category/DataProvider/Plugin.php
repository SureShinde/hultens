<?php
namespace Crealevant\Relevant\Model\Category\DataProvider;
 
class Plugin
{       
    protected $_storeManager;
 
    public function __construct(        
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {       
        $this->_storeManager = $storeManager;    
    }
 
    //retrieve thumnail data for output
    public function afterGetData(\Magento\Catalog\Model\Category\DataProvider $subject, $result)
    {
        $category = $subject->getCurrentCategory();
        $categoryData = $result[$category->getId()];
 
        if (isset($categoryData['thumbnail'])) {
            unset($categoryData['thumbnail']);
            $categoryData['thumbnail'][0]['name'] = $category->getData('thumbnail');
            $categoryData['thumbnail'][0]['url'] = $this->getThumbnailUrl($category->getData('thumbnail'));
        }
 
        $result[$category->getId()] = $categoryData;
 
        return $result;
    }
 
    protected function getThumbnailUrl($imageName){
        $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'catalog/category/' . $imageName;
        return $url;
    }
}

