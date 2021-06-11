<?php

namespace Shreeji\Unusedimages\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class FindUnused {

    /**
     * 
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_urlBuilder;

    /**
     *
     * @var \Magento\Framework\App\ResourceConnection 
     */
    protected $_resource;

    /**
     *
     * @var \Shreeji\Unusedimages\Model\ResourceModel\Unusedimages\CollectionFactory 
     */
    protected $_unusedImage;

    /**
     *
     * @var \Magento\Eav\Model\Config 
     */
    protected $_eavConfig;

    /**
     *
     * @var connection 
     */
    protected $_connection;

    /**
     * Catalog product media config
     *
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $_catalogProductMediaConfig;

    /**
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList  
     */
    protected $_directoryList;

    /**
     *
     * @var dirImages 
     */
    protected $_dirImages = array();
    
    /**
     *
     * @var unusedFactory 
     */
    protected $_unusedFactory;

    /**
     * 
     * @param \Magento\Backend\Model\UrlInterface $urlinterface
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Shreeji\Unusedimages\Model\ResourceModel\Unusedimages\CollectionFactory $unusedImage
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
     * @param DirectoryList $directoryList
     * @param \Shreeji\Unusedimages\Model\UnusedimagesFactory $unusedFactory
     */
    public function __construct(
    \Magento\Backend\Model\UrlInterface $urlinterface, \Magento\Framework\App\ResourceConnection $resource, \Shreeji\Unusedimages\Model\ResourceModel\Unusedimages\CollectionFactory $unusedImage, \Magento\Eav\Model\Config $eavConfig, \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig, \Magento\Framework\App\Filesystem\DirectoryList $directoryList,  \Shreeji\Unusedimages\Model\UnusedimagesFactory $unusedFactory
    ) {
        $this->_urlBuilder = $urlinterface;
        $this->_resource = $resource;
        $this->_unusedImage = $unusedImage;
        $this->_eavConfig = $eavConfig;
        $this->_connection = $this->_resource->getConnection();
        $this->_catalogProductMediaConfig = $catalogProductMediaConfig;
        $this->_directoryList = $directoryList;
        $this->_unusedFactory=$unusedFactory;
    }

    /*
     * Main fuction to find unused image 
     */

    public function findUnusedImages() {
        $connection = $this->_connection;
        $alreadyImages = $this->_unusedImage->create()->getData();
        $alfind=array();
        foreach ($alreadyImages as $alreadyImage) {
            $alfind[] = $alreadyImage['filename'];
        }
        $dbImages = $this->_getProductImageFromDb();
        $dbImages = array_merge($dbImages, $alfind);        
        $mediaPath = 'pub' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product';
        $dirImages = $this->_getDirectoryImages($mediaPath);
        $mainTable = $connection->getTableName('shreeji_unusedimages');
        try {
            foreach ($dirImages as $dirImage) {
                $dirImage = strtr($dirImage, '\\', '/');
                //here we are checking directory image exist in database or not if not then we can get to know that image is unused 
                if (!in_array($dirImage, $dbImages)) { // main logic check
                    // here we are using direct query insted of model for performance improvement
                    $this->_unusedFactory->create()
                            ->setData('filename',$dirImage)
                            ->save();
                    #$sql = "Insert Into  $mainTable  (filename) Values ('$dirImage')";
                    #$connection->query($sql);
                }
            }
        } catch (\Exception $e) {
            throw new \LogicException('Could not save unused image: ' . $e->getMessage());
        }
    }

    /**
     * get image name from database table so we can compare with directory image     
     * @return array
     */
    protected function _getProductImageFromDb() {
        
        $galaryTable = $this->_connection->getTableName('catalog_product_entity_media_gallery_value');
        $galarymedia = "SELECT value_id FROM $galaryTable";
        $_value_ids_db = $this->_connection->fetchAll($galarymedia);
        $_value_ids = array();
        foreach ($_value_ids_db as $_value_id_db) {
            $_value_ids[] = $_value_id_db['value_id'];
        }
        $_value_ids=implode(",",$_value_ids);        
        $mediaTable = $this->_connection->getTableName('catalog_product_entity_media_gallery');
        $querymedia = "SELECT value FROM $mediaTable where value_id IN ($_value_ids)";
        $_imagesdb = $this->_connection->fetchAll($querymedia);
        $_images = array();
        foreach ($_imagesdb as $sigleimage) {
            $_images[] = $sigleimage['value'];
        }
        
        // swatch images 
        $swatchTable = $this->_connection->getTableName('eav_attribute_option_swatch');
        $swatchmedia = "SELECT value FROM $swatchTable";
        $_swatchesdb = $this->_connection->fetchAll($swatchmedia);
        $_imagesswatch = array();
        $_swatchnproduct=array();
        $_nonemptyimagesswatch=array();
        foreach ($_swatchesdb as $sigleswatch) {
            $_imagesswatch[] = $sigleswatch['value'];
        }
        $_nonemptyimagesswatch=array_filter($_imagesswatch);                
        $_swatchnproduct=array_merge($_images,$_nonemptyimagesswatch);
        
        return $_swatchnproduct;
    }

    /**
     * to get all directory image from catalog/product directory 
     * @param type $mediaPath
     * @return array
     */
    protected function _getDirectoryImages($mediaPath) {
        if (is_dir($mediaPath)) {
            if ($dir = opendir($mediaPath)) {
                while (($entry = readdir($dir)) !== false) {
                    if (preg_match('/^\./', $entry) != 1) {
                        // here we are skipping cache, watermark and placeholder directory because magento by default use this directory
                        if (is_dir($mediaPath . DIRECTORY_SEPARATOR . $entry) && !in_array($entry, array('cache', 'watermark', 'placeholder'))) {
                            $this->_getDirectoryImages($mediaPath . DIRECTORY_SEPARATOR . $entry);
                        } elseif (!in_array($entry, array('cache', 'watermark')) && (strpos($entry, '.') != 0)) {
                            $this->_dirImages[] = substr($mediaPath . DIRECTORY_SEPARATOR . $entry, 25);
                        }
                    }
                }
                closedir($dir);
            }
        }
        return $this->_dirImages;
    }

}
