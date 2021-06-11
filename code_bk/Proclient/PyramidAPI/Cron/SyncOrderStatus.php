<?php

namespace Proclient\PyramidAPI\Cron;

use \Psr\Log\LoggerInterface;
use \Proclient\PyramidAPI\Helper\ConfigData;
use \Proclient\PyramidAPI\Helper\SoapSender;
use \Proclient\PyramidAPI\Helper\SoapSenderObject;
use \Proclient\PyramidAPI\Helper\Validation;
use \Magento\Customer\Api\CustomerRepositoryInterface;
use \Magento\Customer\Api\AddressRepositoryInterface;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Sales\Api\InvoiceRepositoryInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class SyncOrderStatus {
  private $logger;
  private $configData;
  private $validation;
  private $soapSender;
  private $soapSenderObject;
  private $orderRepository;
  private $invoiceRepository;
  private $searchCriteriaBuilder;
  private $doDebug;

	public function __construct(
    LoggerInterface $logger,
    ConfigData $configData,
    Validation $validation,
    SoapSender $soapSender,
    SoapSenderObject $soapSenderObject,
    OrderRepositoryInterface $orderRepository,
    InvoiceRepositoryInterface $invoiceRepository,
    SearchCriteriaBuilder $searchCriteriaBuilder
  ) {
    $this->logger = $logger;
    $this->configData = $configData;
    $this->validation = $validation;
    $this->soapSender = $soapSender;
    $this->soapSenderObject = $soapSenderObject;
    $this->orderRepository = $orderRepository;
    $this->invoiceRepository = $invoiceRepository;
    $this->searchCriteriaBuilder = $searchCriteriaBuilder;

    $this->doDebug = $this->configData->getGeneralConfig('debug_log');
    $this->ws_url = $this->configData->getGeneralConfig('wsdl_url');
    $this->ws_username = $this->configData->getAuthConfig('pyramid_username');
    $this->ws_password = $this->configData->getAuthConfig('pyramid_password');
	}

	public function execute() {
    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus starting execution');

    if ($this->ws_url and strlen($this->ws_url) > 0) {
      $invoiced_status = $this->configData->getOrdersConfig('invoiced_status');
      if ($invoiced_status) {
        $this->searchCriteriaBuilder->addFilter('pyramid_sync', null, 'null');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $invoices = $this->invoiceRepository->getList($searchCriteria);

        foreach ($invoices as $invoice) {
          if ($order = $this->orderRepository->get($invoice->order_id)) {
            if ($order->getData('pyramid_sync')) {
              $huvud = $this->soapSenderObject->newOrderhuvud();
              $huvud->webbordernr = $order->getEntityId();
              $huvud->projektstatus = $invoiced_status;
              $pyr_order = $this->soapSender->updateOrder($this->ws_url, $this->ws_username, $this->ws_password, $huvud, $this->doDebug, $this->logger);
              if ($pyr_order == 'ERROR') {
                $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus Could not update order status for order '.$order->getEntityId().' in Pyramid');
              } else {
                $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus Set order status to invoiced for order '.$order->getEntityId().' in Pyramid');
                $invoice->setData('pyramid_sync', date('Y-m-d H:i:s'));
                $this->invoiceRepository->save($invoice);
              }
            }
          }
        }
      }
    } elseif ($this->doDebug) {
      $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus No WS url given, cant send orders');
    }

    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus start fetching updates from Pyramid');
    $path = $this->configData->getArticlesConfig('import_path');
    if ($path and strlen($path) > 0) {
      foreach (glob($path.'*.xml') as $file) {
        if (strpos($file, 'orderstatus_') !== false) {
          if (is_readable($file)) {
            $xml = simplexml_load_file($file);
            if ($xml and isset($xml->orders)) {
              foreach ($xml->orders as $order) {
                if (isset($order->order) and isset($order->order->utlevererad) and isset($order->order->webbordernr)) {
                  if ("".$order->order->utlevererad == 'J') {
                    $magento_orders = $this->getOrderIdByIncrementId("".$order->order->webbordernr);
                    if ($magento_orders and is_array($magento_orders)) {
                      foreach ($magento_orders as $magento_order) {
                        $magento_order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE)->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE);
                        $this->orderRepository->save($magento_order);
                        if ($this->doDebug)
                          $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus set order status complete for '.$order->order->webbordernr);
                      }
                    } else {
                      $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus Could not find order for webbordernummer '.$order->order->webbordernr.' in Magento');
                    }
                  }
                }
              }
              rename($file, $path.'success/'.basename($file));
            } else {
              $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus Could not parse order status file');
              rename($file, $path.'error/'.basename($file));
            }
          } else {
            $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus Could not open order status file');
          }
        }
      }
    } else {
      $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus No import path given, cant get order statuses');
    }
    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus ending execution');
	}

  private function getOrderIdByIncrementId($incrementId) {
    $searchCriteria = $this->searchCriteriaBuilder->addFilter('increment_id', $incrementId)->create();
    try {
      $order = $this->orderRepository->getList($searchCriteria);
      if ($order->getTotalCount()) {
        return $order->getItems();
      }
    } catch (Exception $e)  {
      $this->logger->info('Proclient\PyramidAPI\Cron\SyncOrderStatus Could not get order '.$incrementId.' in Magento');
    }
    return false;
  }
}

?>
