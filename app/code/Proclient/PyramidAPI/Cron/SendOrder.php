<?php

namespace Proclient\PyramidAPI\Cron;

use \Psr\Log\LoggerInterface;
use \Proclient\PyramidAPI\Helper\ConfigData;
use \Proclient\PyramidAPI\Helper\SoapSender;
use \Proclient\PyramidAPI\Helper\SoapSenderObject;
use \Proclient\PyramidAPI\Helper\Validation;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Customer\Api\CustomerRepositoryInterface;
use \Magento\Customer\Api\AddressRepositoryInterface;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Sales\Api\OrderAddressRepositoryInterface;
use \Magento\Sales\Api\OrderItemRepositoryInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\Mail\Template\TransportBuilder;

class SendOrder {

  private $shippingMapper = array(
    'msunifaun_msunifaun_3' => 'DH1',
    'msunifaun_msunifaun_4' => 'DSP',
    'msunifaun_msunifaun_7' => 'DHK',
    'msunifaun_msunifaun_8' => 'POD',
    'msunifaun_msunifaun_10' => 'PNO',
    'msunifaun_msunifaun_11' => 'PHS',
    'msunifaun_msunifaun_12' => 'PHD',
    'msunifaun_msunifaun_13' => 'DP',
  );

  private $logger;
  private $configData;
  private $validation;
  private $soapSender;
  private $productRepository;
  private $customerRepository;
  private $customerAddressRepository;
  private $orderRepository;
  private $orderAddressRepository;
  private $orderItemRepository;
  private $searchCriteriaBuilder;
  private $doDebug;
  private $transportBuilder;

	public function __construct(
    LoggerInterface $logger,
    ConfigData $configData,
    Validation $validation,
    SoapSender $soapSender,
    SoapSenderObject $soapSenderObject,
    ProductRepositoryInterface $productRepository,
    CustomerRepositoryInterface $customerRepository,
    AddressRepositoryInterface $customerAddressRepository,
    OrderRepositoryInterface $orderRepository,
    OrderAddressRepositoryInterface $orderAddressRepository,
    OrderItemRepositoryInterface $orderItemRepository,
    SearchCriteriaBuilder $searchCriteriaBuilder,
    TransportBuilder $transportBuilder
  ) {
    $this->logger = $logger;
    $this->configData = $configData;
    $this->validation = $validation;
    $this->soapSender = $soapSender;
    $this->soapSenderObject = $soapSenderObject;
    $this->productRepository = $productRepository;
    $this->customerRepository = $customerRepository;
    $this->customerAddressRepository = $customerAddressRepository;
    $this->orderRepository = $orderRepository;
    $this->orderAddressRepository = $orderAddressRepository;
    $this->orderItemRepository = $orderItemRepository;
    $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    $this->transportBuilder = $transportBuilder;

    $this->doDebug = $this->configData->getGeneralConfig('debug_log');
    $this->ws_url = $this->configData->getGeneralConfig('wsdl_url');
    $this->ws_username = $this->configData->getAuthConfig('pyramid_username');
    $this->ws_password = $this->configData->getAuthConfig('pyramid_password');
    $this->invoiced_status = $this->configData->getOrdersConfig('invoiced_status');
    $this->invoiced_order_statuses = $this->configData->getOrdersConfig('invoiced_order_statuses');
	}

	public function execute() {
    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder starting execution');

    if (!$this->ws_url or strlen($this->ws_url) < 1) {
      $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder No WS url given, cant send orders');
      if ($this->doDebug)
        $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder ending execution');
      return;
    }

    $send_from = $this->configData->getOrdersConfig('send_from_date');
    if ($send_from and $this->validation->is_date($send_from))
      $this->searchCriteriaBuilder->addFilter('created_at', $send_from, 'gteq');
    $order_statuses = $this->configData->getOrdersConfig('included_order_statuses');
    if ($order_statuses and strlen($order_statuses) > 0)
      $this->searchCriteriaBuilder->addFilter('status', preg_split('/\s*,\s*/', trim($order_statuses)), 'in');
    $this->searchCriteriaBuilder->addFilter('pyramid_sync', null, 'null');
    $searchCriteria = $this->searchCriteriaBuilder->create();

    $orders = $this->orderRepository->getList($searchCriteria);

    if (count($orders) > 0) {
      foreach ($orders as $order) {
        $billing_address = $this->orderAddressRepository->get($order->getData('billing_address_id'));

        $kund = $this->soapSenderObject->newKund();
        if ($customer_id = $order->getCustomerId()) {
          $customer = $this->customerRepository->getById($customer_id);
          $kund->webbforetagskod = $customer_id;
          if ($customer_billing_addr = $customer->getDefaultBilling()) {
            try {
              $default_billing_address = $this->customerAddressRepository->getById($customer_billing_addr);
              $kund->telefon = $default_billing_address->getTelephone();
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
              $kund->telefon = $billing_address->getTelephone();
            }
          } else {
            $kund->telefon = $billing_address->getTelephone();
          }
        } else {
          $kund->telefon = $billing_address->getTelephone();
          $kund->webbforetagskod = 'Guest';  // Talar om att kunden är en gäst
        }

        $kund->foretagskod = $this->validation->phone_to_cust_no($kund->telefon);
        $kund->foretag = $billing_address->getFirstname();
        if (strlen($billing_address->getMiddlename() > 0))
          $kund->foretag .= ' '.$billing_address->getMiddlename();
        $kund->foretag .= ' '.$billing_address->getLastname();

        if ($street = $billing_address->getStreet() and is_array($street) and count($street) > 0)
          $kund->gatuadress = $street[0];
        else
          $kund->gatuadress = null;

        $kund->gatupostadress = $billing_address->getPostcode().' '.$billing_address->getCity();
        $kund->landkod = $billing_address->getCountryId();
        $kund->e_postadress = $billing_address->getEmail();
        $kund->kundtyp = $this->configData->getCustomersConfig('customer_type');
        $kund->kundkategori = $this->configData->getCustomersConfig('customer_category');

        $forsaljning = $this->soapSenderObject->newForsaljning();
        $forsaljning->projekttyp_kund = $this->configData->getCustomersConfig('project_type');

        $kundinfo = $this->soapSenderObject->newKundinformation($kund, $forsaljning);

        // Skicka kundinfo till Pyramid
        $pyr_customer = $this->soapSender->getCustomer($this->ws_url, $this->ws_username, $this->ws_password, $kundinfo, $this->doDebug, $this->logger);
        if ($pyr_customer == 'NOT_FOUND') {
          // Kunden finns inte - skapa!
          $pyr_customer = $this->soapSender->createCustomer($this->ws_url, $this->ws_username, $this->ws_password, $kundinfo, $this->doDebug, $this->logger);
          if ($pyr_customer == 'ERROR') {
            // Felmeddelande från Pyramids API, fortsätt med nästa order
            $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Could not create customer '.$order->getCustomerId().' in Pyramid');
            continue;
          } else {
            $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Created customer '.$order->getCustomerId().' in Pyramid');
          }
        } elseif ($pyr_customer == 'ERROR') {
          // Felmeddelande från Pyramids API, fortsätt med nästa order
          $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Could not fetch customer '.$order->getCustomerId().' from Pyramid');
          continue;
        } else {
          // Kunden finns redan - uppdatera!
          if ($pyr_customer = $this->soapSender->updateCustomer($this->ws_url, $this->ws_username, $this->ws_password, $kundinfo, $this->doDebug, $this->logger)) {
            $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Uppdated customer '.$order->getCustomerId().' in Pyramid');
          } else {
            $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Could not update customer '.$order->getCustomerId().' in Pyramid');
            continue;
          }
        }

        $forsvillkor = $this->soapSenderObject->newForsvillkor();
        $forsvillkor->betalningsvillkor = $this->configData->getOrdersConfig('payment_terms');
        $forsvillkor->expeditionsavgift = 0;  // TODO: ev. lägga till stöd för att beräkna
        $forsvillkor->fraktavgift = $order->getBaseShippingAmount();
        if (array_key_exists($order->getShippingMethod(), $this->shippingMapper)) {
          $forsvillkor->transportsatt = $this->shippingMapper[$order->getShippingMethod()];
        } else {
          $forsvillkor->transportsatt = $order->getShippingMethod();
        }

        $shipping_address = $this->orderAddressRepository->get($order->getData('shipping_address_id'));

        $leveransadress = $this->soapSenderObject->newLeveransadress();
        $leveransadress->leveransadress_1 = $shipping_address->getFirstname().' '.$shipping_address->getLastname();
        $leveransadress->leveransadress_2 = $shipping_address->getCompany();
        if ($street = $shipping_address->getStreet() and is_array($street) and count($street) > 0)
          $leveransadress->leveransadress_3 = $street[0];
        $leveransadress->leveransadress_4 = $shipping_address->getPostcode().' '.$shipping_address->getCity();
        if ($this->configData->getOrdersConfig('use_unifaun')) {
          if ($this->doDebug)
            $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder trying to set Unifaun location');
          $leveransadress->leveransadress_5 = $order->getPickUpLocationName();
          $leveransadress->leveransadress_6 = $order->getPickUpLocationAddress();
          $leveransadress->godsmarkning = $order->getPickUpLocationZipCode().' '.$order->getPickUpLocationCity();
        }
        $leveransadress->landkod = $shipping_address->getCountryId();

        $ovrigt = $this->soapSenderObject->newOvrigt();
        $ovrigt->valutakod = null;
        $currency = $order->getGlobalCurrencyCode();
        if ($currency != 'SEK')
          $ovrigt->valutakod = $currency;

        $huvud = $this->soapSenderObject->newOrderhuvud();
        $huvud->foretagskod = $kund->foretagskod;
        $huvud->webbforetagskod = $kund->webbforetagskod;
        $huvud->webbordernr = $order->getIncrementId();
        $huvud->lager = $this->configData->getOrdersConfig('stockpile');
        $huvud->projekttyp = $this->configData->getCustomersConfig('project_type');
        $huvud->orderdatum = $this->validation->timestamp_to_pyrdate($order->getCreatedAt());
        $huvud->saljare = $this->configData->getOrdersConfig('seller');
        $huvud->er_referens = $kund->foretag;
        $huvud->ert_ordernr = $order->getIncrementId();
        if ($this->invoiced_order_statuses and $this->invoiced_status and (in_array($order->getStatus(), preg_split('/\s*,\s*/', trim($this->invoiced_order_statuses)))))
          $huvud->projektstatus = $this->invoiced_status;
        // TODO: ev. sätta datum från bokad frakt eller från config
        $huvud->onskat_levdatum = $this->validation->timestamp_to_pyrdate($order->getCreatedAt());
        $huvud->forsvillkor = $forsvillkor;
        $huvud->leveransadress = $leveransadress;
        $huvud->ovrigt = $ovrigt;

        $rader = array();
        $textrader = array();
        $this->searchCriteriaBuilder->addFilter('order_id', $order->getEntityId(), 'eq');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $orderRows = $this->orderItemRepository->getList($searchCriteria);
        $radnr = 5;
        foreach ($orderRows as $orderRow) {
          if ($orderRow->getProductType() == 'bundle') {
            $rad = $this->soapSenderObject->newTextrad();
            $rad->radnr = $radnr;
            $rad->fri_text = 'Paketerbjudande: '.$orderRow->getName();
            $textrader[] = $rad;
            $radnr += 5;
          } elseif ($orderRow->getProductType() == 'configurable') {
            $rad = $this->soapSenderObject->newTextrad();
            $rad->radnr = $radnr;
            $rad->fri_text = 'Config.: '.$orderRow->getSku().'; '.$orderRow->getName().'; '.(int)$orderRow->getQtyOrdered().'st; '.$orderRow->getBasePrice().' SEK';
            $textrader[] = $rad;
            $radnr += 5;
          } elseif ($orderRow->getProductType() == 'simple') {
            if ($orderRow->getParentItemId()) {
              $parent = $orderRow->getParentItem();
              if ($parent->getProductType() == 'configurable')
                continue;
            }
            try {
              $product = $this->productRepository->get($orderRow->getSku());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
              $product = false;
            }
            if ($product) {
              if ($product->getData('pyramid_sync')) {
                $rad = $this->soapSenderObject->newOrderrad();
                $rad->radnr = $radnr;
                $rad->artikelkod = $orderRow->getSku();
                $rad->benamning_1 = $orderRow->getName();
                $rad->budget_antal = $orderRow->getQtyOrdered();
                $rad->normalpris = $orderRow->getBasePrice();
                $rad->normalpris_inkl_moms = $orderRow->getBasePriceInclTax();
                $rad->momsbelopp = $orderRow->getBaseTaxAmount();
                if ($discount = $orderRow->getDiscountAmount() and $orderRow->getBasePrice() > 0) {
                  $rad->rabat_procent = 100 * $discount / $orderRow->getBasePrice();
                }
                $rad->belopp = $orderRow->getRowTotal();
                $rad->belopp_inkl_moms = $orderRow->getRowTotalInclTax();
                // Sätter radens lev.datum till samma som i huvudet
                $rad->onskat_levdatum = $huvud->onskat_levdatum;
                $rader[] = $rad;
                $radnr += 5;
              } else {
                $rad = $this->soapSenderObject->newTextrad();
                $rad->radnr = $radnr;
                $rad->fri_text = 'FEL: artikel saknas: '.$orderRow->getSku().'; '.$orderRow->getName().'; Antal: '.$orderRow->getQtyOrdered().'; Pris: '.$orderRow->getBasePrice();
                $textrader[] = $rad;
                $radnr += 5;
              }
            } else {
              $rad = $this->soapSenderObject->newTextrad();
              $rad->radnr = $radnr;
              $rad->fri_text = 'Custom: '.$orderRow->getSku().'; '.$orderRow->getName().'; Antal: '.$orderRow->getQtyOrdered().'; Pris: '.$orderRow->getBasePrice();
              $textrader[] = $rad;
              $radnr += 5;
            }
          }
        }

        if ($meta_textrows = $this->configData->getOrdersConfig('meta_as_textrows') and strlen($meta_textrows) > 0) {
          $meta_textrows = explode(',', $meta_textrows);
          foreach ($meta_textrows as $meta_textrow) {
            if ($meta_value = $order->getData($meta_textrow) and strlen($meta_value) > 0) {
              $rad = $this->soapSenderObject->newTextrad();
              $rad->radnr = $radnr;
              $rad->fri_text = $meta_textrow.': '.$meta_value;
              $textrader[] = $rad;
              $radnr += 5;
            }
          }
        }

        if ($meta_fields = $this->configData->getOrdersConfig('meta_as_field') and strlen($meta_fields) > 0) {
          $meta_fields = explode(',', $meta_fields);
          foreach ($meta_fields as $meta_field) {
            if ($meta_value = $order->getData($meta_field) and strlen($meta_value) > 0) {
              if (!isset($huvud->kundunikt))
                $huvud->kundunikt = array();
              $huvud->kundunikt[$meta_field] = $meta_value;
            }
          }
        }

        $pyr_order = $this->soapSender->createOrder($this->ws_url, $this->ws_username, $this->ws_password, $huvud, $rader, $textrader, $this->doDebug, $this->logger);
        if ($pyr_order == 'ERROR') {
          $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Could not create order '.$order->getEntityId().' in Pyramid');
          //$this->send_error_mail();
        } elseif ($pyr_order == 'MISSING_ARTICLE') {
          $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Could not create order '.$order->getEntityId().' in Pyramid because one of the products dont exist');
          //$this->send_error_mail();
        } elseif ($pyr_order == 'ALREADY_CREATED') {
          $order->setData('pyramid_sync', date('Y-m-d H:i:s'));
          $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Order '.$order->getEntityId().' already created in Pyramid');
        } elseif ($pyr_order == 'ORDER_CREATED') {
          $order->setData('pyramid_sync', date('Y-m-d H:i:s'));
          $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Created order '.$order->getEntityId().' in Pyramid but did not get ordernr in return');
        } else {
          $order->setData('pyramid_orderno', $pyr_order);
          $order->setData('pyramid_sync', date('Y-m-d H:i:s'));
          $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder Created order '.$order->getEntityId().' in Pyramid');
        }
        $this->orderRepository->save($order);
      }
    } else {
      $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder No new orders to send');
    }

    if ($this->doDebug)
      $this->logger->info('Proclient\PyramidAPI\Cron\SendOrder ending execution');
	}

  private function send_error_mail() {
    if ($mail = $this->configData->getGeneralConfig('error_mail')) {
      $transport = $this->transportBuilder->setTemplateIdentifier('pyramidapi_error_email')
        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
        ->setFrom('general')
        ->addTo($mail)
        ->getTransport();
      $transport->sendMessage();
    }
  }
}

?>
