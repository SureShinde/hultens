<?php
namespace Proclient\PyramidAPI\Cron;

use \Psr\Log\LoggerInterface;
use \Proclient\PyramidAPI\Helper\ConfigData;
use \Magento\CatalogInventory\Api\StockRegistryInterface;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Sales\Api\OrderItemRepositoryInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Api\FilterBuilder;
use \Magento\Framework\Api\Search\FilterGroupBuilder;

class GetStock {
  private $logger;
  private $configData;
  private $stockRegistry;
  private $orderRepository;
  private $orderItemRepository;
  private $searchCriteriaBuilder;
  private $filterBuilder;
  private $filterGroupBuilder;
  private $doDebug;

  public function __construct(
    LoggerInterface $logger,
    ConfigData $configData,
    StockRegistryInterface $stockRegistry,
    OrderRepositoryInterface $orderRepository,
    OrderItemRepositoryInterface $orderItemRepository,
    SearchCriteriaBuilder $searchCriteriaBuilder,
    FilterBuilder $filterBuilder,
    FilterGroupBuilder $filterGroupBuilder
  ) {
    $this->logger = $logger;
    $this->configData = $configData;
    $this->stockRegistry = $stockRegistry;
    $this->orderRepository = $orderRepository;
    $this->orderItemRepository = $orderItemRepository;
    $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    $this->filterBuilder = $filterBuilder;
    $this->filterGroupBuilder = $filterGroupBuilder;
    $this->doDebug = $this->configData->getGeneralConfig('debug_log');
  }

  public function execute() {
    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\GetStock starting execution');
    $path = $this->configData->getArticlesConfig('import_path');
    if ($path and strlen($path) > 0) {
      foreach (glob($path.'*.xml') as $file) {
        if (strpos($file, 'saldo') !== false) {
          if (is_readable($file)) {
            $xml = simplexml_load_file($file);
            if ($xml and isset($xml->artiklar) and isset($xml->artiklar->saldo) and isset($xml->meta) and isset($xml->meta->tidsstampel)) {
              $artiklar = $xml->artiklar->saldo;
              $row_errors = false;

              $sales_to_subtract = array();
              $ts = date('Y-m-d', strtotime(substr($xml->meta->tidsstampel, 0, 14)));
              $searchOrder = $this->searchCriteriaBuilder
                ->addFilter('pyramid_sync', true, 'null')
                ->addFilter('created_at', $ts, 'gt')
                ->create();
              $orders = $this->orderRepository->getList($searchOrder);
              foreach ($orders as $order) {
                $searchOrderItem = $this->searchCriteriaBuilder
                  ->addFilter('order_id', $order->getIncrementId(), 'eq')
                  ->create();
                $orderRows = $this->orderItemRepository->getList($searchOrderItem);
                foreach ($orderRows as $orderRow) {
                  if (!isset($sales_to_subtract[$orderRow->getSku()]))
                    $sales_to_subtract[$orderRow->getSku()] = 0;
                  $sales_to_subtract[$orderRow->getSku()] += $orderRow->getQtyOrdered();
                }
              }

              foreach ($artiklar as $antal) {
                if (isset($antal['kod'])) {
                  $art_no = "".$antal['kod'];
                  try {
                    $stockItem = $this->stockRegistry->getStockItemBySku($art_no);
                  } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $stockItem = false;
                  }
                  if ($stockItem) {
                    $initial_stock_status = $stockItem->getIsInStock();
                    if (array_key_exists($art_no, $sales_to_subtract))
                      $antal = $antal - $sales_to_subtract[$art_no];
                    if ($antal <= 0) {
                      $antal = 0;
                      $stockItem->setIsInStock((bool)$initial_stock_status);
                    } else {
                      $stockItem->setIsInStock(true);
                    }
                    $stockItem->setQty($antal);
                    $this->stockRegistry->updateStockItemBySku($art_no, $stockItem);
                    $this->logger->info('Proclient\PyramidAPI\Cron\GetStock Set stock for sku '.$art_no.' to '.$antal);
                  }
                } else {
                  $row_errors = true;
                }
              }
              if ($row_errors) {
                $this->logger->info('Proclient\PyramidAPI\Cron\GetStock Stock file contains unparseable data');
                rename($file, $path.'error/'.basename($file));
              }
              rename($file, $path.'success/'.basename($file));
            } else {
              $this->logger->info('Proclient\PyramidAPI\Cron\GetStock Could not parse stock file');
              rename($file, $path.'error/'.basename($file));
            }
          } else {
            $this->logger->info('Proclient\PyramidAPI\Cron\GetStock Could not open stock file');
          }
        }
      }
    } else {
      $this->logger->info('Proclient\PyramidAPI\Cron\GetStock No import path given, cant get stock');
    }
    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\GetStock ending execution');
  }
}

?>
