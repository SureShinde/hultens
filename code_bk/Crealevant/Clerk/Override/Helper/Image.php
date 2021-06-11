<?php

namespace Crealevant\Clerk\Override\Helper;

use Clerk\Clerk\Helper\Image as ParentClass;
use Clerk\Clerk\Model\Config;
use Magento\Catalog\Model\Product;

class Image extends ParentClass
{
    /*
     * Reason for override: Fix the url issue when image hasn't "/" at the beginning
     */
    public function getUrl(Product $item)
    {
        $imageUrl = null;

        //Get image thumbnail from settings
        $imageType = $this->scopeConfig->getValue(Config::XML_PATH_PRODUCT_SYNCHRONIZATION_IMAGE_TYPE);
        $helper = $this->helperFactory->create();

        if ($imageType) {
            /** @var \Magento\Catalog\Helper\Image $helper */
            $imageUrl = $helper->init($item, $imageType)->getUrl();;
            if ($imageUrl == $helper->getDefaultPlaceholderUrl()) {
                // allow to try other types
                $imageUrl = null;
            }
        }

        if (!$imageUrl) {
            $store = $this->storeManager->getStore();
            $itemImage = $item->getImage() ?? $item->getSmallImage() ?? $item->getThumbnail();

            if ($itemImage === 'no_selection') {
                $imageUrl = $helper->getDefaultPlaceholderUrl('small_image');
            } else {
                // Start override
                if(substr($itemImage, 0, 1) != "/") {
                    $itemImage = "/" . $itemImage;
                }
                // End override

                $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $itemImage;
            }
        }

        return $imageUrl;
    }
}
