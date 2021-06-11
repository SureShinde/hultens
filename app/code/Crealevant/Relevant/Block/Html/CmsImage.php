<?php

namespace Crealevant\Relevant\Block\Html;

use Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;

/**
 * Class CmsImage
 * @package Crealevant\Relevant\Block\Html
 */
class CmsImage extends Template
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $page;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $__storeManager;

    /**
     * CmsImage constructor.
     * @param Context $context
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Cms\Model\Page $page,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) {
        $this->page = $page;
        $this->_filesystem = $filesystem;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCmsImage() {
        $media_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imageFile = $this->page->getData('cms_background_image');

        $imageUrl = $media_url."cms/background/tmp/".$imageFile;
        return $imageUrl;
    }
    public function getPageTitle() {
        $pageId = $this->page->getId();
        $pageTitle = $this->page->getData('title');
        return $pageTitle;
    }

    /**
     * @return bool
     */
    public function isCmsImage() {
        $isCmsImage = false;
        if($this->page->getData('cms_background_image')) {
            $isCmsImage = true;
        }
        return $isCmsImage;
    }
    /**
     * @return bool
     */
    public function isCmsBkgDescription() {
        $isCmsBkgDescription = false;
        if($this->page->getData('cms_background_description')) {
            $isCmsBkgDescription = true;
        }
        return $isCmsBkgDescription;
    }
    public function cmsBkgDescription() {
        $cmsBkgDescription = '';
        if($this->page->getData('cms_background_description')) {
            $cmsBkgDescription = $this->page->getData('cms_background_description');
        }
        return $cmsBkgDescription;
    }
    public function cmsBkgLink() {
        $cmsBkgLink = '';
        if($this->page->getData('cms_background_link')) {
            $cmsBkgLink = $this->page->getData('cms_background_link');
        }
        return $cmsBkgLink;
    }
    public function isCmsBkgLink() {
        $isCmsBkgLink = false;
        if($this->page->getData('cms_background_link')) {
            $isCmsBkgLink = true;
        }
        return $isCmsBkgLink;
    }
}