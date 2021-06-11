<?php
namespace Proclient\PyramidAPI\Cron;

use \Psr\Log\LoggerInterface;
use \Proclient\PyramidAPI\Helper\ConfigData;
use \Proclient\PyramidAPI\Helper\SoapSender;
use \Proclient\PyramidAPI\Helper\SoapSenderObject;
use \Magento\Catalog\Api\Data\ProductInterfaceFactory;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Framework\App\ResourceConnection;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Catalog\Model\ResourceModel\Product;

class GetArticles {
  private $logger;
  private $configData;
  private $soapSender;
  private $soapSenderObject;
  private $productFactory;
  private $productRepository;
  private $resource;
  private $productF;
  private $productResourceModel;
  private $doDebug;

  public function __construct(
    LoggerInterface $logger,
    ConfigData $configData,
    SoapSender $soapSender,
    SoapSenderObject $soapSenderObject,
    ProductInterfaceFactory $productFactory,
    ProductRepositoryInterface $productRepository,
    ResourceConnection $resource,
    ProductFactory $productF,
    Product $productResourceModel
  ) {
    $this->logger = $logger;
    $this->configData = $configData;
    $this->soapSender = $soapSender;
    $this->soapSenderObject = $soapSenderObject;
    $this->productFactory = $productFactory;
    $this->productRepository = $productRepository;
    $this->resource = $resource;
    $this->productF = $productF;
    $this->productResourceModel = $productResourceModel;

    $this->doDebug = $this->configData->getGeneralConfig('debug_log');
    $this->ws_url = $this->configData->getGeneralConfig('wsdl_url');
    $this->ws_username = $this->configData->getAuthConfig('pyramid_username');
    $this->ws_password = $this->configData->getAuthConfig('pyramid_password');
  }

  public function execute() {
    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles starting execution');
    if ($this->configData->getArticlesConfig('articles_use_ws')) {
      if (!$this->ws_url or strlen($this->ws_url) < 1) {
        $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles No WS url given, cant send orders');
        if ($this->doDebug)
          $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles ending execution');
        return;
      }
      $articles = $this->soapSender->getArticles($this->ws_url, $this->ws_username, $this->ws_password);
      if ($articles == 'ERROR') {
        $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Could not fetch articles from Pyramid');
      } else {
        if ($articles !== false and count($articles) > 0) {
          foreach ($articles as $article) {
            try {
              $product = $this->productRepository->get("".$article->artikelkod);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
              $product = false;
            }
            if ($product) {
              if (isset($article->artikel) and isset($article->artikel->pris) and isset($article->artikel->pris->butikspris)) {
                if (isset($article->artikel->pris->valutagrundpris) and isset($article->artikel->pris->valutagrundpris->grundpris)) {
                  // Förbestämd valuta per site, börja med att sätta default-priset utan shop
                  $this->updateProductPrice($product->getId(), false, $article->artikel->pris->butikspris);
                  $this->updateProductPrice($product->getId(), 1, $article->artikel->pris->butikspris);
                  foreach ($article->artikel->pris->valutagrundpris->grundpris as $grundpris) {
                    if ($grundpris['valuta'] == 'DKK') {
                      $this->updateProductPrice($product->getId(), 2, "".$grundpris);
                    } elseif ($grundpris['valuta'] == 'EUR') {
                      $this->updateProductPrice($product->getId(), 3, "".$grundpris);
                    } elseif ($grundpris['valuta'] == 'NOK') {
                      $this->updateProductPrice($product->getId(), 4, "".$grundpris);
                    }
                  }
                } elseif ($store_view_string = $this->configData->getArticlesConfig('store_view_price') and strlen($store_view_string) > 0) {
                  $store_views = preg_split('/\s*,\s*/', trim($store_view_string));
                  foreach ($store_views as $store_view) {
                    $this->updateProductPrice($product->getId(), $store_view, $article->artikel->pris->butikspris);
                  }
                } else {
                  $this->updateProductPrice($product->getId(), false, $article->artikel->pris->butikspris);
                }
              }
              $meta_values = array();
              if (isset($article->sokoptimering) and isset($article->sokoptimering->title))
                $meta_values['title'] = "".$article->sokoptimering->title;
              if (isset($article->sokoptimering) and isset($article->sokoptimering->sokord))
                $meta_values['keyword'] = "".$article->sokoptimering->sokord;
              if (isset($article->sokoptimering) and isset($article->sokoptimering->beskrivning))
                $meta_values['description'] = "".$article->sokoptimering->beskrivning;
              if (isset($article->artikel) and isset($article->artikel->ovrigt) and isset($article->artikel->ovrigt->fraktdragvikt))
                $meta_values['weight'] = "".$article->artikel->ovrigt->fraktdragvikt;
              if (isset($article->artikel) and isset($article->artikel->ovrigt) and isset($article->artikel->ovrigt->levartkod))
                $meta_values['hu_lev_art'] = "".$article->artikel->ovrigt->levartkod;
              if (isset($article->artikel) and isset($article->artikel->ovrigt) and isset($article->artikel->ovrigt->ean_kod)) {
                if ($ean_meta_key = $this->configData->getArticlesConfig('articles_set_ean') and strlen($ean_meta_key) > 0) {
                  $meta_values[$ean_meta_key] = "".$article->artikel->ovrigt->ean_kod;
                }
              }
              $this->updateProductMeta($product->getId(), $meta_values);
              $product->setData('pyramid_sync', date('Y-m-d H:i:s'));
              $this->productRepository->save($product);
            } else {
              if ($this->configData->getArticlesConfig('articles_create_new')) {
                if ($this->doDebug)
                  $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Article '.$article->artikelkod.' does not exist, creating new');
                $product = $this->productFactory->create();
                $product->setSku("".$article->artikelkod);
                $product->setName("".$article->webbbenamning);
                if (isset($article->sokoptimering) and isset($article->sokoptimering->urlkey)) {
                  if (!$this->url_key_exists($article->sokoptimering->urlkey)) {
                    $product->setUrlKey("".$article->sokoptimering->urlkey);
                  } else {
                    $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles UrlKey '.$article->sokoptimering->urlkey.' already exists (Article '.$article->artikelkod.'), creating new');
                    $product->setUrlKey($this->create_url_key("".$article->webbbenamning));
                  }
                } else {
                  $product->setUrlKey($this->create_url_key("".$article->webbbenamning));
                }
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                if (isset($article->sokoptimering) and isset($article->sokoptimering->title))
                  $product->setMetaTitle("".$article->sokoptimering->title);
                if (isset($article->sokoptimering) and isset($article->sokoptimering->sokord))
                  $product->setMetaKeyword("".$article->sokoptimering->sokord);
                if (isset($article->sokoptimering) and isset($article->sokoptimering->beskrivning))
                  $product->setMetaDescription("".$article->sokoptimering->beskrivning);
                if (isset($article->artikel) and isset($article->artikel->pris) and isset($article->artikel->pris->butikspris))
                  $product->setPrice("".$article->artikel->pris->butikspris);
                if (isset($article->artikel) and isset($article->artikel->ovrigt) and isset($article->artikel->ovrigt->fraktdragvikt))
                  $product->setWeight("".$article->artikel->ovrigt->fraktdragvikt);
                if (isset($article->artikel) and isset($article->artikel->ovrigt) and isset($article->artikel->ovrigt->levartkod))
                  $product->setData('hu_lev_art', "".$article->artikel->ovrigt->levartkod);
                if (isset($article->artikel) and isset($article->artikel->ovrigt) and isset($article->artikel->ovrigt->ean_kod)) {
                  if ($ean_meta_key = $this->configData->getArticlesConfig('articles_set_ean') and strlen($ean_meta_key) > 0) {
                    $product->setData($ean_meta_key, "".$article->artikel->ovrigt->ean_kod);
                  }
                }
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                $product->setData('inventory_green_status_text', 'Leverans 2-7 arbetsdagar');
                $product->setData('pyramid_artno', $article->artikelkod);
                $product->setData('pyramid_sync', date('Y-m-d H:i:s'));
                $new_product = $this->productRepository->save($product);
                if (isset($article->artikel->pris->valutagrundpris) and isset($article->artikel->pris->valutagrundpris->grundpris)) {
                  // Förbestämd valuta per site
                  foreach ($article->artikel->pris->valutagrundpris->grundpris as $grundpris) {
                    if ($grundpris['valuta'] == 'DKK') {
                      $this->updateProductPrice($new_product->getId(), 2, "".$grundpris);
                    } elseif ($grundpris['valuta'] == 'EUR') {
                      $this->updateProductPrice($new_product->getId(), 3, "".$grundpris);
                    } elseif ($grundpris['valuta'] == 'NOK') {
                      $this->updateProductPrice($new_product->getId(), 4, "".$grundpris);
                    }
                  }
                }
              } else {
                if ($this->doDebug)
                  $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Article '.$article->artikelkod.' does not exist, skipping create according to configuration');
              }
            }
          }
          $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Imported new articles from Pyramid');
        } else {
          $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Found no new articles in Pyramid');
        }
      }
    } else {
      $path = $this->configData->getArticlesConfig('import_path');
      if ($path and strlen($path) > 0) {
        foreach (glob($path.'*.xml') as $file) {
          if (strpos($file, 'artiklar_') !== false) {
            if (is_readable($file)) {
              $xml = file_get_contents($file);
              $xml = str_replace("w20:","",$xml);
              $xml = simplexml_load_string($xml);
              if (isset($xml->artiklar) and isset($xml->artiklar->e_artikel)) {
                foreach ($xml->artiklar->e_artikel as $e_artikel) {
                  try {
                    $product = $this->productRepository->get("".$e_artikel->artikelkod);
                  } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $product = false;
                  }
                  if ($product) {
                    if ($this->doDebug)
                      $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Article '.$e_artikel->artikelkod.' does exist, updating values');
                    if (isset($e_artikel->artikel) and isset($e_artikel->artikel->pris) and isset($e_artikel->artikel->pris->butikspris)) {
                      if (isset($e_artikel->artikel->pris->valutagrundpris) and isset($e_artikel->artikel->pris->valutagrundpris->grundpris)) {
                        // Förbestämd valuta per site
                        foreach ($e_artikel->artikel->pris->valutagrundpris->grundpris as $grundpris) {
                          if ($grundpris['valuta'] == 'DKK') {
                            $this->updateProductPrice($product->getId(), 2, "".$grundpris);
                          } elseif ($grundpris['valuta'] == 'EUR') {
                            $this->updateProductPrice($product->getId(), 3, "".$grundpris);
                          } elseif ($grundpris['valuta'] == 'NOK') {
                            $this->updateProductPrice($product->getId(), 4, "".$grundpris);
                          }
                        }
                        $this->updateProductPrice($product->getId(), 1, "".$e_artikel->artikel->pris->butikspris);
                      } elseif ($store_view_string = $this->configData->getArticlesConfig('store_view_price') and strlen($store_view_string) > 0) {
                        $store_views = preg_split('/\s*,\s*/', trim($store_view_string));
                        foreach ($store_views as $store_view) {
                          $this->updateProductPrice($product->getId(), $store_view, "".$e_artikel->artikel->pris->butikspris);
                        }
                      } else {
                        $this->updateProductPrice($product->getId(), false, "".$e_artikel->artikel->pris->butikspris);
                      }
                    }
                    if ($this->doDebug)
                      $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles fetching meta values');
                    $meta_values = array();
                    if (isset($e_artikel->sokoptimering) and isset($e_artikel->sokoptimering->title))
                      $meta_values['title'] = "".$e_artikel->sokoptimering->title;
                    if (isset($e_artikel->sokoptimering) and isset($e_artikel->sokoptimering->sokord))
                      $meta_values['keyword'] = "".$e_artikel->sokoptimering->sokord;
                    if (isset($e_artikel->sokoptimering) and isset($e_artikel->sokoptimering->beskrivning))
                      $meta_values['description'] = "".$e_artikel->sokoptimering->beskrivning;
                    if (isset($e_artikel->artikel) and isset($e_artikel->artikel->ovrigt) and isset($e_artikel->artikel->ovrigt->fraktdragvikt))
                      $meta_values['weight'] = "".$e_artikel->artikel->ovrigt->fraktdragvikt;
                    if (isset($e_artikel->artikel) and isset($e_artikel->artikel->ovrigt) and isset($e_artikel->artikel->ovrigt->levartkod))
                      $meta_values['hu_lev_art'] = "".$e_artikel->artikel->ovrigt->levartkod;
                    if (isset($e_artikel->artikel) and isset($e_artikel->artikel->ovrigt) and isset($e_artikel->artikel->ovrigt->ean_kod)) {
                      if ($ean_meta_key = $this->configData->getArticlesConfig('articles_set_ean') and strlen($ean_meta_key) > 0) {
                        $meta_values[$ean_meta_key] = "".$e_artikel->artikel->ovrigt->ean_kod;
                      }
                    }
                    $this->updateProductMeta($product->getId(), $meta_values);
                    $product->setData('pyramid_sync', date('Y-m-d H:i:s'));
                    $this->productRepository->save($product);
                    if ($this->doDebug)
                      $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles meta values saved');
                  } else {
                    if ($this->configData->getArticlesConfig('articles_create_new')) {
                      if ($this->doDebug)
                        $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Article '.$e_artikel->artikelkod.' does not exist, creating new');
                      $product = $this->productFactory->create();
                      $product->setSku("".$e_artikel->artikelkod);
                      $product->setName("".$e_artikel->webbbenamning);
                      if (isset($e_artikel->sokoptimering) and isset($e_artikel->sokoptimering->urlkey)) {
                        if (!$this->url_key_exists($e_artikel->sokoptimering->urlkey)) {
                          $product->setUrlKey("".$e_artikel->sokoptimering->urlkey);
                        } else {
                          $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles UrlKey '.$e_artikel->sokoptimering->urlkey.' already exists (Article '.$e_artikel->artikelkod.'), creating new');
                          $product->setUrlKey($this->create_url_key("".$e_artikel->webbbenamning));
                        }
                      } else {
                        $product->setUrlKey($this->create_url_key("".$e_artikel->webbbenamning));
                      }
                      $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                      $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                      if (isset($e_artikel->sokoptimering) and isset($e_artikel->sokoptimering->title))
                        $product->setMetaTitle("".$e_artikel->sokoptimering->title);
                      if (isset($e_artikel->sokoptimering) and isset($e_artikel->sokoptimering->sokord))
                        $product->setMetaKeyword("".$e_artikel->sokoptimering->sokord);
                      if (isset($e_artikel->sokoptimering) and isset($e_artikel->sokoptimering->beskrivning))
                        $product->setMetaDescription("".$e_artikel->sokoptimering->beskrivning);
                      $product->setPrice('0');
                      if (isset($e_artikel->artikel) and isset($e_artikel->artikel->pris) and isset($e_artikel->artikel->pris->butikspris))
                        $product->setPrice("".$e_artikel->artikel->pris->butikspris);
                      if (isset($e_artikel->artikel) and isset($e_artikel->artikel->ovrigt) and isset($e_artikel->artikel->ovrigt->fraktdragvikt))
                        $product->setWeight("".$e_artikel->artikel->ovrigt->fraktdragvikt);
                      if (isset($e_artikel->artikel) and isset($e_artikel->artikel->ovrigt) and isset($e_artikel->artikel->ovrigt->levartkod))
                        $product->setData('hu_lev_art', "".$e_artikel->artikel->ovrigt->levartkod);
                      if (isset($e_artikel->artikel) and isset($e_artikel->artikel->ovrigt) and isset($e_artikel->artikel->ovrigt->ean_kod)) {
                        if ($ean_meta_key = $this->configData->getArticlesConfig('articles_set_ean') and strlen($ean_meta_key) > 0) {
                          $product->setData($ean_meta_key, "".$e_artikel->artikel->ovrigt->ean_kod);
                        }
                      }
                      $product->setAttributeSetId(4); // Default attribute set for products
                      $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                      $product->setData('inventory_green_status_text', 'Leverans 2-7 arbetsdagar');
                      $product->setData('pyramid_artno', "".$e_artikel->artikelkod);
                      $product->setData('pyramid_sync', date('Y-m-d H:i:s'));
                      $new_product = $this->productRepository->save($product);
                      if (isset($e_artikel->artikel->pris->valutagrundpris) and isset($e_artikel->artikel->pris->valutagrundpris->grundpris)) {
                        // Förbestämd valuta per site
                        foreach ($e_artikel->artikel->pris->valutagrundpris->grundpris as $grundpris) {
                          if ($grundpris['valuta'] == 'DKK') {
                            $this->updateProductPrice($new_product->getId(), 2, "".$grundpris);
                          } elseif ($grundpris['valuta'] == 'EUR') {
                            $this->updateProductPrice($new_product->getId(), 3, "".$grundpris);
                          } elseif ($grundpris['valuta'] == 'NOK') {
                            $this->updateProductPrice($new_product->getId(), 4, "".$grundpris);
                          }
                        }
                      }
                    } else {
                      if ($this->doDebug)
                        $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Article '.$e_artikel->artikelkod.' does not exist, skipping create according to configuration');
                    }
                  }
                }
                rename($file, $path.'success/'.basename($file));
                $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Imported new articles Pyramid');
              } else {
                rename($file, $path.'error/'.basename($file));
                $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Could not parse articles file');
              }
            } else {
              $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles Could not open articles file');
            }
          }
        }
      } else {
        $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles No import path given, cant get articles');
      }
    }
    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles ending execution');
  }

  private function create_url_key($product_name) {
    $product_name = strtolower($product_name);
    $search = array('å','ä','ö');
    $replace = array('a','a','o');
    $product_name = str_replace($search, $replace, $product_name);
    $url = preg_replace('#[^0-9a-z]+#i', '-', $product_name);
    $addon = '';
    $counter = 0;
    while ($this->url_key_exists($url.$addon)) {
      $counter++;
      $addon = '-'.$counter;
    }
    return $url.$addon;
  }

  private function url_key_exists($url) {
    $suffix = $this->configData->getConfigValue('catalog/seo/product_url_suffix');
    if (strlen($suffix) > 0)
      $url .= $suffix;
    $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
    $tablename = $connection->getTableName('url_rewrite');
    $sql = $connection->select()->from(['url_rewrite' => $connection->getTableName('url_rewrite')], ['request_path'])->where('request_path = (?)', $url);
    if (!empty($connection->fetchAssoc($sql)))
      return true;
    return false;
  }

  private function updateProductPrice($productId, $storeId, $price) {
    try {
      $product = $this->productF->create();
      $this->productResourceModel->load($product, $productId);
      if ($storeId)
        $product->setStoreId($storeId);
      $product->setPrice($price);
      $this->productResourceModel->saveAttribute($product, 'price');
    } catch (\Exception $e) {
      return false;
    }
    return true;
  }

  private function updateProductMeta($productId, $meta_values) {
    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles trying to update product meta for '.$productId);
    try {
      $product = $this->productF->create();
      $this->productResourceModel->load($product, $productId);
      $product->setMetaTitle($meta_values['title']);
      $product->setMetaKeyword($meta_values['keyword']);
      $product->setMetaDescription($meta_values['description']);
      $product->setWeight($meta_values['weight']);
      $product->setData('hu_lev_art', $meta_values['hu_lev_art']);
      $product->save();
    } catch (\Exception $e) {
      if ($this->doDebug)
        $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles could not update product meta for '.$productId);
      return false;
    }
    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\GetArticles updated product meta for '.$productId);
    return true;
  }
}

?>
