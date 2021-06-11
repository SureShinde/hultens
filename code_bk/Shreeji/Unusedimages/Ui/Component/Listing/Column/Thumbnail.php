<?php

namespace Shreeji\Unusedimages\Ui\Component\Listing\Column;

use Magento\Catalog\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column {

    const NAME = 'filename';
    const ALT_FIELD = 'name';

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface 
     */
    protected $storeManager;

    /**
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     *
     * @var \Magento\Framework\UrlInterface 
     */
    protected $urlBuilder;

    /**
     * 
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Image $imageHelper
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
    ContextInterface $context, UiComponentFactory $uiComponentFactory, Image $imageHelper, UrlInterface $urlBuilder, StoreManagerInterface $storeManager, array $components = [], array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * 
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $mediaRelativePath = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
            foreach ($dataSource['data']['items'] as & $item) {
                $url = '';
                $url = $mediaRelativePath . $item['filename'];
                $item[$fieldName . '_src'] = $url;
                $item[$fieldName . '_alt'] = $this->getAlt($item) ? : '';                
                $item[$fieldName . '_orig_src'] = $url;
            }
        }

        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row) {
        $altField = $this->getData('config/altField') ? : self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }

}
