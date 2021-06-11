<?php
/**
 *
 */

/**
 *
 */
namespace Mediastrategi\Unifaun\Helper;

/**
 *
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var string
     */
    const API_URL = 'https://api.unifaun.com/rs-extapi/v1/';

    /**
     * @var string
     */
    const ABOUT_URL = 'http://www.mediastrategi.se/';

    /**
     * @var string
     */
    const ABOUT_EMAIL = 'kontakt@mediastrategi.se';

    /**
     * @var string
     */
    const ABOUT_ADDRESS = 'Sturegatan 13, 722 13, Västerås, Sweden';

    /**
     * @var string
     */
    const ABOUT_PHONE = '(+46) 021-470 88 30';

    /**
     * @var string
     */
    const ABOUT_LOGO = 'https://license.mediastrategi.se/logo-200x31.png';

    /**
     * @var string
     */
    const DYNAMIC_FIELD_CUSTOMER_PHONE = 'customer_phone';

    /**
     * @var string
     */
    const DYNAMIC_FIELD_CUSTOMER_EMAIL = 'customer_email';

    /**
     * @var string
     */
    const DYNAMIC_FIELD_ORDER_CURRENCY_CODE = 'order_currency_code';

    /**
     * @var string
     */
    const DYNAMIC_FIELD_ORDER_GRAND_TOTAL = 'order_grand_total';

    /**
     * @var string
     */
    const DYNAMIC_FIELD_ORDER_SUBTOTAL = 'order_sub_total';

    /**
     * @var string
     */
    const DYNAMIC_FIELD_ORDER_TOTAL_QUANTITY = 'order_total_quantity';

    /**
     * @var string
     */
    const DYNAMIC_FIELD_ORDER_TOTAL_WEIGHT = 'order_total_weight';

    /**
     * @var string
     */
    const DYNAMIC_FIELD_ORDER_TAX_AMOUNT = 'order_tax_amount';

    /**
     * @var string
     */
    const DYNAMIC_FIELD_ORDER_TOTAL_ITEM_COUNT = 'order_total_item_count';

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @var \Mediastrategi\Unifaun\Model\ShippingmethodFactory $_shippingMethodFactory
     */
    protected $_shippingMethodFactory;

    /**
     * @var \Mediastrategi\Unifaun\Model\ShipmentFactory $_shipmentFactory
     */
    protected $_shipmentFactory;

    /**
     * @var string
     */
    protected $_latestError = '';

    /**
     * @internal
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @internal
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * @internal
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @internal
     * @static
     * @var array
     */
    private static $_orderShipmentResponses = array();

    /**
     * @internal
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @internal
     * var \Magento\Directory\Helper\Data
     */
    private $directoryHelper;

    /**
     * @internal
     * @var string
     */
    private $restDirectory = '';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingMethodFactory
     * @param \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory
     * @param \Magento\Sales\Model\Order $orderModel
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Helper\Data $directoryHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingMethodFactory,
        \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Helper\Data $directoryHelper
    ) {
        $this->_moduleList = $moduleList;
        $this->_shippingMethodFactory = $shippingMethodFactory;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_orderModel = $orderModel;
        $this->_productRepository = $productRepository;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->directoryHelper = $directoryHelper;
        $this->restDirectory = dirname(dirname(__FILE__)) . '/Libraries/Unifaun_Online/src/';
        parent::__construct($context);
    }

    /**
     *
     */
    public function getAbout()
    {
        return '<div><ul style="list-style: circle inside none;"><li><strong>URL:</strong> <a href="' . self::ABOUT_URL . '">' . self::ABOUT_URL . '</a></li><li><strong>Phone:</strong> ' . self::ABOUT_PHONE . '</li><li><strong>E-mail:</strong> <a href="mailto:' . self::ABOUT_EMAIL . '">' . self::ABOUT_EMAIL . '</a></li><li><strong>Address:</strong> ' . self::ABOUT_ADDRESS . '</li></ul><p style="text-align: center; margin: 20px 0 10px 0;"><a href="' . self::ABOUT_URL . '" target="_blank"><img src="' . self::ABOUT_LOGO . '" /></a></p></div>';
    }

    /**
     * @return string
     */
    public function getLatestError()
    {
        return $this->_latestError;
    }

    /**
     * @param string $services
     * @return bool|string
     */
    public function getServiceCarrier($service)
    {
        $carrierServices = $this->getCarrierServices();
        foreach ($carrierServices as $key => $options) {
            if (is_array($options)
                && isset($options['services'][$service])
            ) {
                return $key;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getServiceAddons()
    {
        $this->loadRestHelper();
        return \Mediastrategi\UnifaunOnline\Helper::getServicesAddons();
    }

    /**
     *
     */
    private function loadRestHelper()
    {
        require_once($this->restDirectory . 'Helper.php');
    }

    /**
     *
     */
    private function loadRestLibrary()
    {
        require_once($this->restDirectory . 'Rest.php');
    }

    /**
     * @param string $service
     * @return string|bool
     */
    public function getCarrierByService($service)
    {
        $this->loadRestHelper();
        return \Mediastrategi\UnifaunOnline\Helper::getCarrierByService($service);
    }

    /**
     * @return array
     */
    public function getCarriersWithPickUpLocation()
    {
        $this->loadRestHelper();
        return \Mediastrategi\UnifaunOnline\Helper::getCarriersWithPickUpLocation();
    }

    /**
     * @return array
     */
    public function getCarriersWithPickUpLocationOptions()
    {
        $this->loadRestHelper();
        if ($carriers = \Mediastrategi\UnifaunOnline\Helper::getCarriersWithPickUpLocation()) {
            $options = [
                0 => [
                    'label' => __('No'),
                    'value' => 0,
                ],
            ];
            foreach ($carriers as $carrier => $title) {
                $options[$carrier] = [
                    'label' => $title,
                    'value' => $carrier,
                ];
            }
            return $options;
        }
        return [];
    }

    /**
     * @return array
     */
    public function getAddons()
    {
        $this->loadRestHelper();
        return \Mediastrategi\UnifaunOnline\Helper::getServicesAddons();
    }

    /**
     * @param string $countryCode
     * @param string $carrier
     * @param string $zip
     * @param string [$street = '']
     * @return array|bool
     */
    public function getPickUpLocations($countryCode, $carrier, $zip, $street = '')
    {
        $this->log(sprintf(
            '%s getPickUpLocations() "%s" "%s" "%s" "%s"',
            __METHOD__,
            $countryCode,
            $carrier,
            $zip,
            $street
        ));
        $agents = false;
        if (!empty($countryCode)
            && !empty($carrier)
            && !empty($zip)
        ) {
            $this->loadRestLibrary();
            $rest = new \Mediastrategi\UnifaunOnline\Rest(array(
                'uri' => self::API_URL,
                'user_id' => $this->getStoreConfig(
                    'carriers/msunifaun/credentials/username'
                ),
                'username' => $this->getStoreConfig(
                    'carriers/msunifaun/credentials/username'
                ),
                'password' => $this->getStoreConfig(
                    'carriers/msunifaun/credentials/password'
                )
            ));
            try {
                if ($rest->addressesAgentsGet(array(
                    'countryCode' => $countryCode,
                    'type' => $carrier,
                    'zip' => $zip,
                    'street' => $street,
                ))) {
                    $agents = $rest->getLastDecodedResponse();
                    $this->log(sprintf(
                        '%s REST response: %s',
                        __METHOD__,
                        $rest->getLastResponse()
                    ));
                }
            } catch (\Exception $e) {
                $this->log(__(
                    'Error occured while fetching agents %1',
                    $e->getMessage()
                ));
            }
        }
        return $agents;
    }

    /**
     * This method will put all packages into the same delivery.
     *
     * @param \Magento\Framework\DataObject $request
     * @throws \Exception
     * @return \Magento\Framework\DataObject
     * @see \Magento\Fedex\Model\Carrier::_doShipmentRequest()
     */
    public function createShipment($request)
    {
        $orderId = $request->getOrderShipment()->getOrderId();

        if (isset(self::$_orderShipmentResponses[$orderId])) {
            $this->log(__(
                'Found cache for order shipment response %1',
                (int) $orderId
            ));
            return self::$_orderShipmentResponses[$orderId];
        }

        $order = $this->_orderModel->load($orderId);
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        // Create order reference based on settings
        $orderRefenceSetting = $this->getStoreConfig(
            'carriers/msunifaun/options/order_reference',
            $order->getStoreId()
        );
        if ($orderRefenceSetting == 'number') {
            $orderReference = $request
                ->getOrderShipment()
                ->getOrder()
                ->getIncrementId();
        } else {
            $orderReference = $orderId;
        }

        $this->log(__METHOD__ . ' $request: ' . print_r($request->debug(), true) . ', order: ' . print_r($order->debug(), true) . ', shippingAddress: ' . print_r($shippingAddress->debug(), true) . ', billingAddress: ' . print_r($billingAddress->debug(), true));

        $explode = explode('_', $request->getShippingMethod());
        $shippingMethodId = (int) $explode[1];
        $model = $this->_shippingMethodFactory->create();
        $shippingMethod = $model->load($shippingMethodId);

        if (empty($shippingMethod->getData())) {
            throw new \Exception(__(
                'Failed to find shipping method %1',
                (string) $shippingMethodId
            ));
        }

        // Collect pick-up location (if any) and shipping-method has it enabled
        $pickUpLocationId = null;
        if ($shippingMethod->getData('pickup')) {
            $pickUpLocationId = (string) $order->getPickUpLocationId();
        }

        $service = $shippingMethod->getData('method');
        $carrier = $this->getServiceCarrier($service);

        $addons = json_decode($shippingMethod->getData('addons'), true);
        if (!empty($addons)) {
            $dataAddons = [];
            foreach (array_keys($addons) as $key) {
                $dataAddons[] = [
                    "id" => $key,
                ];
            }
        } else {
            $dataAddons = false;
        }

        /*
        die(sprintf(
        'Shipment request: %s',
        print_r($request->debug(), true)
        ));
        */
        $isStateRequired = $this->directoryHelper->isRegionRequired(
            $request->getRecipientAddressCountryCode());

        $data = [
            "pdfConfig" => [
                "target1Media" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target1Media',
                    $order->getStoreId()
                ),
                "target1XOffset" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target1XOffset',
                    $order->getStoreId()
                ),
                "target1YOffset" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target1YOffset',
                    $order->getStoreId()
                ),
                "target2Media" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target2Media',
                    $order->getStoreId()
                ),
                "target2XOffset" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target2XOffset',
                    $order->getStoreId()
                ),
                "target2YOffset" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target2YOffset',
                    $order->getStoreId()
                ),
                "target3Media" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target3Media',
                    $order->getStoreId()
                ),
                "target3XOffset" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target3XOffset',
                    $order->getStoreId()
                ),
                "target3YOffset" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target3YOffset',
                    $order->getStoreId()
                ),
                "target4Media" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target4Media',
                    $order->getStoreId()
                ),
                "target4XOffset" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target4XOffset',
                    $order->getStoreId()
                ),
                "target4YOffset" => $this->getStoreConfig(
                    'carriers/msunifaun/pdfconfig/target4YOffset',
                    $order->getStoreId()
                ),
            ],
            "shipment" => [
                "sender" => [
                    "quickId" => $this->getStoreConfig(
                        'carriers/msunifaun/credentials/quick_id',
                        $order->getStoreId()
                    ),
                ],
                "senderPartners" => [[
                    "id" => $carrier,
                ]],
                "parcels" => $this->_getRequestParcels(
                    $request,
                    $order->getStoreId()
                ),
                "orderNo" => $orderReference,
                "receiver" => [
                    "address1" => $request->getRecipientAddressStreet1(),
                    "address2" => $request->getRecipientAddressStreet2(),
                    "city" => $request->getRecipientAddressCity(),
                    "contact" => $request->getRecipientContactPersonName(),
                    "country" => $request->getRecipientAddressCountryCode(),
                    "email" => $request->getRecipientEmail(),
                    "name" => ($request->getRecipientContactCompanyName()
                        ?: $request->getRecipientContactPersonName()),
                    "phone" => $request->getRecipientContactPhoneNumber(),
                    "state" => ($isStateRequired
                        ? $request->getRecipientAddressStateOrProvinceCode()
                        : ''),
                    "zipcode" => $request->getRecipientAddressPostalCode(),
                ],
                "senderReference" => $this->_getSenderReference(
                    $orderReference,
                    $order->getStoreId()
                ),
                "service" => ($dataAddons ? [
                    "id" => $service,
                    "addons" => $dataAddons,
                ] : ["id" => $service]),
                "receiverReference" => '',
                "options" => [[
                    "errorTo" => $this->getStoreConfig(
                        'carriers/msunifaun/options/error_to',
                        $order->getStoreId()
                    ),
                    "from" => $this->getStoreConfig(
                        'carriers/msunifaun/options/from',
                        $order->getStoreId()
                    ),
                    "id" => $this->getStoreConfig(
                        'carriers/msunifaun/options/id',
                        $order->getStoreId()
                    ),
                    "languageCode" => $this->getStoreConfig(
                        'carriers/msunifaun/options/language_code',
                        $order->getStoreId()
                    ),
                    "message" => (string) __(
                        "This is order number %1",
                        $orderReference
                    ),
                    "sendEmail" => ($this->getStoreConfig(
                        'carriers/msunifaun/options/send_email',
                        $order->getStoreId()
                    ) ? true : false),
                    "to" => $this->getStoreConfig(
                        'carriers/msunifaun/options/to',
                        $order->getStoreId()
                    ),
                ]]
            ]
        ];

        $createStoredShipment = !empty($shippingMethod->getData('stored_shipment'));

        // Do we have a pick-up location?
        if (!empty($pickUpLocationId)) {
            $this->log(__METHOD__ . ' using pick up location ' . $pickUpLocationId);
            $data['shipment']['agent'] = [
                'quickId' => $pickUpLocationId,
            ];
        } else {
            $this->log(__METHOD__ . ' not using pick up location since one or more values are missing');
        }

        $this->log(__METHOD__ . ' Shipment $data before applying Carrier, Service and Add-ons: ' . print_r($data, true) . print_r($addons, true));
        $this->_applyCarrierService($carrier, $service, $addons, $request, $data, $order);
        $this->log(__METHOD__ . ' Shipment $data after applying Carrier, Service and Add-ons: ' . print_r($data, true));

        // Apply extra settings (if any)
        if ($extra = $shippingMethod->getData('extra')) {
            $jsonDecodedExtra = json_decode($extra, true);
            if ($jsonDecodedExtra !== null
                && is_array($jsonDecodedExtra)
                && count($jsonDecodedExtra) > 0
            ) {
                $extraData = [
                    'shipment' => $jsonDecodedExtra,
                ];
                $data = array_merge_recursive($data, $extraData);
                $this->log(__METHOD__ . ' Shipment $data after applying extra settings: ' . print_r($extraData, true) . ', data: ' . print_r($data, true));
            }
        }

        $customsStatNoAttribute = $this->getStoreConfig(
            'carriers/msunifaun/customs/statno',
            $order->getStoreId()
        );
        $customsOriginCountryAttribute = $this->getStoreConfig(
            'carriers/msunifaun/customs/origin_country',
            $order->getStoreId()
        );
        $customsDeclarationEnabled = $shippingMethod->getData('customs_enabled');
        $customsDocuments = ($shippingMethod->getData('customs_documents') ?
            json_decode($shippingMethod->getData('customs_documents'), true) :
            []);
        if (!empty($customsDeclarationEnabled)) {
            $customsDeclaration = array();

            // Iterate products, gather HS-codes and map customs value, weight, copies, products and origin country to it
            if (!empty($customsStatNoAttribute)) {
                $statNoData = array();
                if ($packages = $request->getOrderShipment()->getPackages()) {
                    foreach ($packages as $package)
                    {
                        if (!empty($package['items'])
                            && is_array($package['items'])
                        ) {
                            foreach ($package['items'] as $item)
                            {
                                if (!empty($item['product_id'])) {
                                    if ($product = $this->_productRepository->getById(
                                        $item['product_id'])
                                    ) {
                                        if ($statNo = $product->getData($customsStatNoAttribute)) {
                                            if (!isset($statNoData[$statNo])) {
                                                $statNoData[$statNo] = array(
                                                    'products' => array(),
                                                    'copies' => 0,
                                                    'netWeight' => 0,
                                                    'value' => 0,
                                                );
                                            }
                                            $statNoData[$statNo]['products'][] = $item;

                                            if (!empty($item['qty'])) {
                                                $statNoData[$statNo]['copies'] += $item['qty'];
                                            }

                                            if (!empty($item['customs_value'])) {
                                                $statNoData[$statNo]['value'] += $item['customs_value'];
                                            }

                                            if (!empty($item['weight'])) {
                                                $statNoData[$statNo]['netWeight'] += $item['weight'];
                                            }

                                            if ($originCountry = $product->getData($customsOriginCountryAttribute)) {
                                                $statNoData[$statNo]['sourceCountryCode'] = $originCountry;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($statNoData)) {
                        $customsDeclaration['lines'] = array();
                        foreach ($statNoData as $statNo => $statNoData)
                        {
                            $line = array(
                                'contents' => $this->_getPackageContents(
                                    $statNoData['products'],
                                    $order->getStoreId()
                                ),
                                'copies' => 1, // Meaning this line only occurs once
                                'netWeight' => $statNoData['netWeight'],
                                'statNo' => $statNo,
                                'value' => $statNoData['value'],
                                'valuesPerItem' => false,
                            );
                            if (!empty($statNoData['sourceCountryCode'])) {
                                $line['sourceCountryCode'] = $statNoData['sourceCountryCode'];
                            }
                            $customsDeclaration['lines'][] = $line;
                        }
                    }
                }
            }

            if (!empty($customsDocuments)) {
                $customsDeclaration['printSet'] = $customsDocuments;
            }

            if (!empty($customsDeclaration)) {
                $data['shipment']['customsDeclaration'] = $customsDeclaration;
            }

            /* die(sprintf(
                'customs declaration: %s for request: %s',
                print_r($customsDeclaration, true),
                print_r($request->debug(), true)
            )); */
        }

        // die($carrier . ' ' . $service . ' <pre>' . print_r($data, true) . '</pre>');

        $this->loadRestLibrary();
        $rest = new \Mediastrategi\UnifaunOnline\Rest(array(
            'uri' => self::API_URL,
            'user_id' => $this->getStoreConfig(
                'carriers/msunifaun/credentials/user_id',
                $order->getStoreId()
            ),
            'username' => $this->getStoreConfig(
                'carriers/msunifaun/credentials/username',
                $order->getStoreId()
            ),
            'pacsoft' => ($this->getStoreConfig(
                'carriers/msunifaun/pacsoft',
                $order->getStoreId()) ? true : false),
            'password' => $this->getStoreConfig(
                'carriers/msunifaun/credentials/password',
                $order->getStoreId()
            )
        ));

        $error = '';
        $test = false;
        try {
            $test = $createStoredShipment
                ? $rest->storedShipmentsPost($data['shipment'])
                : $rest->shipmentsPost($data);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $response = $rest->getLastResponse();
        $this->log(__METHOD__ . ' Shipment $response ' . print_r($response, true));

         /* die("<pre>request:\n" . print_r($rest->getLastRequest(), true) . "\n,\nresponse:\n" . print_r($response, true) . "\n,\nresponse-code:\n" . $rest->getLastResponseCode() . "\n,stored-shipment:\n" . $createStoredShipment . "\n,\ntest:" . $test . "\n,decoded:\n" . print_r($rest->getLastDecodedResponse(), true) . "\n</pre>"); */

        if ($test)  {

            $restRequest = $rest->getLastRequest();
            $responseCode = $rest->getLastResponseCode();
            $shipmentNumber = $rest->getLastShipmentNumber();
            $lastShippingLabel = $rest->getLastShippingLabel();
            $trackingUrl = $rest->getLastTrackingUrl();
            $decodedResponse = $rest->getLastDecodedResponse();

            // die('trackingUrl: ' . $trackingUrl);

            $result = new \Magento\Framework\DataObject();
            $result->setGatewayResponse($response);

            if (!empty($shipmentNumber)) {
                $result->setTrackingNumber($shipmentNumber);
            }
            if (!empty($lastShippingLabel)) {
                $result->setShippingLabelContent($lastShippingLabel);
            } else if ($createStoredShipment) {
                // TODO Magento 2 doesn't work properly without a label
                $result->setShippingLabelContent($this->getShippingLabelDummyPdf());
            }

            $this->_saveShipment(
                $decodedResponse,
                $carrier,
                $orderId,
                $trackingUrl
            );
        } else {

            $restRequest = $rest->getLastRequest();
            $responseCode = $rest->getLastResponseCode();
            $error .= $rest->getLastErrorMessage();

            if ($decodedResponse = $rest->getLastDecodedResponse()) {
                $newError = '<ul>';
                if (!empty($decodedResponse)) {
                    $newErrors = 0;
                    foreach ($decodedResponse as $errorItem)
                    {
                        if (!empty($errorItem['statuses'])
                            && is_array($errorItem['statuses'])
                        ) {
                            foreach ($errorItem['statuses'] as $subErrorItem)
                            {
                                if ($newError .= $this->getRowError($subErrorItem)) {
                                    $newErrors++;
                                }
                            }
                        } else {
                            if ($newError .= $this->getRowError($errorItem)) {
                                $newErrors++;
                            }
                        }
                    }
                    $newError .= '</ul>';
                    if (!empty($newErrors)) {
                        $error = $newError;
                    }
                }
            }

            $error .= sprintf(
                '<p style="word-break: break-all;"><strong>Request:</strong><br />%s</p>',
                $restRequest
            );

            $error .= sprintf(
                '<p style="word-break: break-all;"><strong>Response:</strong><br />%s</p>',
                $rest->getLastResponse()
            );

            // die('error: ' . $newError . '<pre>' . print_r($data, true) . '</pre>');

            if ($responseCode == 401) {
                if (!empty($newError)) {
                    $error .= 'Invalid or expired token.';
                } else {
                    $error = 'Invalid or expired token.';
                }
            } else if ($responseCode == 403) {
                if (!empty($newError)) {
                    $error .= "The token is valid but it doesn't grant access to the operation attempted.";
                } else {
                    $error = "The token is valid but it doesn't grant access to the operation attempted.";
                }
            }

            if (!empty($error)) {
                throw new \Magento\Framework\Exception\LocalizedException(__(
                    'Failed request with error (%1): %2',
                    $responseCode,
                    $error
                ));
                $result->setErrors([$error]);
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__(
                    'Failed request with no error, response: %1 (%2), request: %3',
                    print_r($decodedResponse, true),
                    $responseCode.
                        print_r($data, true)
                ));
            }
        }

        self::$_orderShipmentResponses[$orderId] = $result;
        $this->log(__(
            'Saved cache for order shipment response %1',
            (int) $orderId
        ));

        return self::$_orderShipmentResponses[$orderId];
    }

    /**
     * @param array $row
     * @return string
     */
    private function getRowError($row)
    {
        if (!empty($row['message'])
            && isset($row['location'])
        ) {
            return sprintf(
                '<li>%s%s</li>',
                $row['message'],
                (!empty($row['location'])
                    ? ' (' . $row['location'] . ')'
                    : (!empty($row['field'])
                        ? ' (' . $row['field'] . ')'
                        : '')
                )
            );
        }
        return '';
    }

    /**
     * @return array
     */
    public function getAllContainerTypes()
    {
        $this->loadRestHelper();
        return \Mediastrategi\UnifaunOnline\Helper::getAllContainerTypes();
    }

    /**
     * @param string $key
     * @param int|null [$storeId = null]
     */
    public function getStoreConfig($key, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            isset($storeId)
            ? $storeId
            : $this->_storeManager->getStore()->getId()
        );
    }

    /**
     * @param \Magento\Framework\DataObjectArray [$params = null]
     * @return array|bool
     */
    public function getContainerTypes($params = null)
    {
        if (isset($params)
            && $params->getMethod()
        ) {
            $explode = array_map('trim', explode('_', $params->getMethod()));
            $id = (int) $explode[1];
            $this->log('getContainerTypes() id: ' . $id);
            $model = $this->_shippingMethodFactory->create();
            $shippingMethod = $model->load($id);
            if ($service = $shippingMethod->getData('method')) {
                $this->log('Found shipping service: ' . $service);
                $this->loadRestHelper();
                if ($carrierContainerTypes =
                    \Mediastrategi\UnifaunOnline\Helper::getContainerTypesByService($service)
                ) {
                    return $carrierContainerTypes;
                }
            }
        }
        return [];
    }

    /**
     * Check if enabled
     *
     * @param int|null [$storeId = null]
     * @return string|null
     */
    public function isEnabled($storeId = null)
    {
        return $this->getStoreConfig(
            'carriers/msunifaun/active',
            $storeId
        );
    }

    /**
     * @param string $path
     * @return bool
     */
    public function hasConfig($path)
    {
        return $this->scopeConfig->isSetFlag(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $path
     * @param mixed|null [$default = null]
     * @return mixed
     */
    public function getConfig($path, $default = null)
    {
        if ($this->hasConfig($path)) {
            return $this->scopeConfig->getValue(
                $path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $default;
    }

    /**
     * @param int|null [$storeId = null]
     * @return mixed
     */
    public function getDebugStatus($storeId = null)
    {
        return $this->getStoreConfig(
            'carriers/msunifaun/debug',
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getExtensionVersion()
    {
        if ($moduleInfo = $this->_moduleList->getOne('Mediastrategi_Unifaun')) {
            return $moduleInfo['setup_version'];
        }
        return '-';
    }

    /**
     * In format: carrier-id => array(service-code, ..)
     *
     * @return array
     */
    public function getCarrierServices()
    {
        $this->loadRestHelper();
        return \Mediastrategi\UnifaunOnline\Helper::getCarrierServices();
    }

    /**
     * @return array
     */
    public function getCarrierServicesOptions()
    {
        $services = $this->getCarrierServices();
        $formattedServices = [];
        foreach ($services as $key => $options) {
            if (is_array($options)
                && !empty($options['title'])
                && !empty($options['services'])
            ) {
                $formattedServices[$key] = [
                    'label' => sprintf(
                        '%s (%s)',
                        $options['title'],
                        $key
                    ),
                    'value' => [],
                ];
                foreach ($options['services'] as $subkey => $value) {
                    $formattedServices[$key]['value'][$subkey] = [
                        'label' => sprintf(
                            '%s (%s)',
                            $value['title'],
                            $subkey
                        ),
                        'value' => $subkey,
                    ];
                }
            } elseif (is_string($options)) {
                $formattedServices[$key] = (string) $options;
            }
        }
        return $formattedServices;
    }

    /**
     * @param string $message
     */
    public function log($message)
    {
        if ($this->getDebugStatus()) {
            $this->_logger->debug(
                date('Y-m-d H:i:s') . ' - ' . $message . "\n"
            );
        }
    }

    /**
     * @return array
     * @todo This should dynamically get all custom fields on accounts and orders.
     */
    public function getDynamicFields()
    {
        return [
            self::DYNAMIC_FIELD_CUSTOMER_PHONE => __('Customer Phone Number'),
            self::DYNAMIC_FIELD_CUSTOMER_EMAIL => __('Customer E-mail Address'),
            self::DYNAMIC_FIELD_ORDER_CURRENCY_CODE => __('Order Currency Code'),
            self::DYNAMIC_FIELD_ORDER_GRAND_TOTAL => __('Order Grand Total Amount'),
            self::DYNAMIC_FIELD_ORDER_SUBTOTAL => __('Order Subtotal Amount'),
            self::DYNAMIC_FIELD_ORDER_TOTAL_QUANTITY => __('Order Total Items Ordered'),
            self::DYNAMIC_FIELD_ORDER_TOTAL_WEIGHT => __('Order Total Items Weight'),
            self::DYNAMIC_FIELD_ORDER_TAX_AMOUNT => __('Order Tax Amount'),
            self::DYNAMIC_FIELD_ORDER_TOTAL_ITEM_COUNT => __('Order Total Item Count'),
        ];
    }

    /**
     * @param DataObject $shipment
     * @param string $userId
     */
    public function getShipmentTrackingLink($shipment, $userId)
    {
        $trackingLink = $shipment->getData('tracking_link');
        return (!empty($trackingLink)
            ? urldecode($trackingLink)
            : $this->getTrackingLink(
                $shipment->getRcvCountry(),
                $userId,
                $shipment->getReference()
            )
        );
    }

    /**
     * This functions add compatibility with older versions of module
     *
     * @param string $receiverCountry
     * @param string $userId
     * @param string $reference
     * @return string
     */
    public function getTrackingLink($receiverCountry, $userId, $reference)
    {
        $prefix = 'https://www.unifaunonline.com/ext.uo.se.se.track';
        if ($receiverCountry == 'FI') {
            $prefix = 'https://www.unifaunonline.com/ext.uo.fi.fi.track';
        } elseif ($receiverCountry == 'DK') {
            $prefix = 'https://www.unifaunonline.com/ext.uo.dk.dk.track';
        }
        return sprintf(
            '%s?key=%s&reference=%s',
            $prefix,
            $userId,
            $reference
        );
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getShippingLabelDummyPdf()
    {
        $path = dirname(dirname(__FILE__)) . '/Libraries/shipping_label_dummy.pdf';
        if (file_exists($path)) {
            return file_get_contents($path);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__(
                'Failed to shipping label dummy at %1',
                $path
            ));
        }
    }

    /**
     * @internal
     * @param int $orderId
     * @param int|null [$storeId = null]
     * @return string
     */
    private function _getSenderReference($orderId, $storeId = null)
    {
        $prefix = $this->getStoreConfig(
            'carriers/msunifaun/options/sender_reference_prefix',
            $storeId
        );
        if (empty($prefix)) {
            $prefix = 'msunifaun_';
        }
        return $prefix . $orderId;
    }

    /**
     * @internal
     * @param \Magento\Shipping\Model\Shipment\Request $request
     * @param int|null [$storeId = null]
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _getRequestParcels($request, $storeId = null)
    {
        $parcels = [];
        if ($packages = $request->getOrderShipment()->getPackages()) {
            foreach ($packages as $package) {
                // die('request: ' . print_r($request->debug(), true) . print_r($packageParams, true));

                // Replace comma with dot
                $weight = (float) str_replace(
                    ',',
                    '.',
                    $package['params']['weight']
                );

                // Convert pounds to kilos
                if (!empty($package['params']['weight_units'])
                    && $package['params']['weight_units'] !== 'KILOGRAM'
                ) {
                    if ($package['params']['weight_units'] === 'POUND') {
                        $weight *= 0.45359237;
                    }
                }

                // Convert inches and centimetres to meters
                $height = (float) str_replace(
                    ',',
                    '.',
                    $package['params']['height']
                );
                $length = (float) str_replace(
                    ',',
                    '.',
                    $package['params']['length']
                );
                $width = (float) str_replace(
                    ',',
                    '.',
                    $package['params']['width']
                );
                if (!empty($package['params']['dimension_units'])
                    && $package['params']['dimension_units'] !== 'METER'
                ) {
                    if ($package['params']['dimension_units'] === 'INCH') {
                        $height *= 0.0254;
                        $length *= 0.0254;
                        $width *= 0.0254;
                    } else if ($package['params']['dimension_units'] === 'CENTIMETER') {
                        $height /= 100;
                        $length /= 100;
                        $width /= 100;
                    }
                }
                $weight = round($weight, 2);
                $length = round($length, 2);
                $width = round($width, 2);
                $height = round($height, 2);

                $parcels[] = [
                    'copies' => '1',
                    'weight' => (string) ($weight ? $weight : ''),
                    'length' => (string) ($length ? $length : ''),
                    'width' => (string) ($width ? $width : ''),
                    'height' => (string) ($height ? $height : ''),
                    'contents' => $this->_getPackageContents(
                        $package['items'],
                        $storeId
                    ),
                    'valuePerParcel' => true,
                    'packageCode' => (string) $package['params']['container'],
                ];
                // die('parcels: ' . print_r($parcels, true));
            }
        } else {
            if (!count($parcels)) {
                throw new \Magento\Framework\Exception\LocalizedException(__(
                    'Request generated no parcels, request: %1',
                    print_r($request->debug(), true)
                ));
            }
        }
        return $parcels;
    }

    /**
     * @internal
     * @param array $items
     * @param int|null [$storeId = null]
     * @return string
     */
    private function _getPackageContents($packageItems, $storeId = null)
    {
        $parcelContentsFormat = $this->getStoreConfig(
            'carriers/msunifaun/options/parcel_contents',
            $storeId
        );
        if (empty($parcelContentsFormat)) {
            $parcelContentsFormat = 'categories';
        }
        if ($parcelContentsFormat === 'empty') {
            return '';
        }

        $tmp = [];
        $uniqueCategories = [];
        foreach ($packageItems as $item)
        {
            if (empty($item['product_id'])
                || $parcelContentsFormat === 'products'
            ) {
                $tmp[] = $item['name'];
            } else if ($parcelContentsFormat === 'categories') {
                if ($product = $this->_productRepository->getById($item['product_id'])) {

                    $collection = $this->_categoryCollectionFactory->create();
                    $collection->addAttributeToSelect('*');
                    $collection->addIsActiveFilter();

                    if ($categoryIds = $product->getCategoryIds()) {
                        if ($categories = $collection->addAttributeToFilter('entity_id', $categoryIds)) {
                            foreach ($categories as $category)
                            {
                                if ($category->getLevel() > 1
                                    && $category->getParentCategory()->getIsActive()
                                    && !isset($uniqueCategories[$category->getName()])
                                ) {
                                    $tmp[] = $category->getName();
                                    $uniqueCategories[$category->getName()] = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return (!empty($tmp) ? implode(',', $tmp) : '');
    }

    /**
     * @internal
     * @param string $carrier
     * @param string $service
     * @param array $addons
     * @param \Magento\Framework\DataObject $request
     * @param array $data
     * @param \Magento\Sales\Model\Order $order
     * @throws \Exception
     */
    private function _applyCarrierServiceGeneral($carrier, $service, $addons, $request, & $data, $order)
    {
        // Apply general add-on logic here
        if (isset($data['shipment']['service']['addons'])) {
            foreach ($data['shipment']['service']['addons'] as &$addon) {
                if (!empty($addon['id'])
                    && isset($addons[$addon['id']])
                    && is_array($addons[$addon['id']])
                    && count($addons[$addon['id']])
                ) {
                    foreach ($addons[$addon['id']] as $elementId => $value) {
                        if (stripos($elementId, '_dynamic') === false) {
                            if (!empty($addons[$addon['id']][$elementId . '_dynamic'])) {

                                // Apply dynamic value for field here
                                $key = $addons[$addon['id']][$elementId . '_dynamic'];

                                if ($key == self::DYNAMIC_FIELD_CUSTOMER_PHONE) {
                                    $addon[$elementId] = $request->getRecipientContactPhoneNumber();
                                } elseif ($key == self::DYNAMIC_FIELD_CUSTOMER_EMAIL) {
                                    $addon[$elementId] = $request->getRecipientEmail();
                                } elseif ($key == self::DYNAMIC_FIELD_ORDER_CURRENCY_CODE) {
                                    $addon[$elementId] = $order->getOrderCurrencyCode();
                                } elseif ($key == self::DYNAMIC_FIELD_ORDER_GRAND_TOTAL) {
                                    $addon[$elementId] = $order->getGrandTotal();
                                } elseif ($key == self::DYNAMIC_FIELD_ORDER_SUBTOTAL) {
                                    $addon[$elementId] = $order->getSubtotal();
                                } elseif ($key == self::DYNAMIC_FIELD_ORDER_TOTAL_QUANTITY) {
                                    $addon[$elementId] = $order->getTotalQtyOrdered();
                                } elseif ($key == self::DYNAMIC_FIELD_ORDER_TOTAL_WEIGHT) {
                                    $addon[$elementId] = $order->getWeight();
                                } elseif ($key == self::DYNAMIC_FIELD_ORDER_TAX_AMOUNT) {
                                    $addon[$elementId] = $order->getTaxAmount();
                                } elseif ($key == self::DYNAMIC_FIELD_ORDER_TOTAL_ITEM_COUNT) {
                                    $addon[$elementId] = $order->getTotalItemCount();
                                } else {
                                    throw new \Exception(sprintf(
                                        'Invalid dynamic field key "%s"',
                                        $key
                                    ));
                                }
                                $this->log(sprintf(
                                    __METHOD__ . ' Add-on field "%s" "%s" "%s" "%s" was assigned dynamic value "%s" based on key "%s" and order "%s"',
                                    $carrier,
                                    $service,
                                    $addon['id'],
                                    $elementId,
                                    $addon[$elementId],
                                    $key,
                                    print_r($order->debug(), true)
                                ));
                            } elseif (isset($value)
                                && $value !== ''
                            ) {
                                // Apply static value for field here
                                $addon[$elementId] = $value;
                                $this->log(sprintf(
                                    __METHOD__ . ' Add-on field "%s" "%s" "%s" "%s" was assigned static value "%s"',
                                    $carrier,
                                    $service,
                                    $addon['id'],
                                    $elementId,
                                    $addon[$elementId]
                                ));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @internal
     * @param string $carrier
     * @param string $service
     * @param array $addons
     * @param \Magento\Framework\DataObject $request
     * @param array $data
     * @param \Magento\Sales\Model\Order $order
     */
    private function _applyCarrierServiceSpecific($carrier, $service, $addons, $request, & $data, $order)
    {
        // Apply specific add-on logic here
        $path = __DIR__ . '/Library/CarrierServices/' . $carrier . '_' . $service . '.php';
        if (file_exists($path)) {
            $this->log(sprintf(
                'Specific add-on logic file "%s" exists',
                $path
            ));
            $this->loadRestHelper();
            $className = '\\Mediastrategi\\Unifaun\\Helper\\Library\\CarrierServices\\' . $carrier . '_' . $service;
            try {
                require_once($path);
                if (class_exists($className, false)) {
                    $class = new $className();
                    $this->log(sprintf(
                        'Class for specific add-on logic exists "%s"',
                        $className
                    ));
                    if (method_exists($class, 'apply')) {
                        $class->apply($request, $data, $addons, $order);
                        $this->log(sprintf(
                            __METHOD__ . ' Data after applying specific carrier-service "%s" "%s" with add-ons "%s" and data "%s" and order "%s"',
                            $carrier,
                            $service,
                            print_r($addons, true),
                            print_r($data, true),
                            print_r($order->debug(), true)
                        ));
                    }
                }
            } catch (\Exception $e) {
                $this->log(sprintf(
                    'Exception when loading class: %s, exception: %s',
                    $className,
                    $e->getMessage()
                ));
            }
        }
    }

    /**
     * @internal
     * @param string $carrier
     * @param string $service
     * @param array $addons
     * @param \Magento\Framework\DataObject $request
     * @param array $data
     * @param \Magento\Sales\Model\Order $order
     */
    private function _applyCarrierService($carrier, $service, $addons, $request, & $data, $order)
    {
        $this->_applyCarrierServiceGeneral($carrier, $service, $addons, $request, $data, $order);
        $this->_applyCarrierServiceSpecific($carrier, $service, $addons, $request, $data, $order);
    }

    /**
     * @internal
     * @param array $response
     * @param string $carrier
     * @param int $orderId
     * @param string $trackingLink
     */
    private function _saveShipment($response, $carrier, $orderId, $trackingLink)
    {
        $shipment = $this->_shipmentFactory->create();

        $shipment->setPartnerId($carrier);
        $shipment->setOrderId($orderId);
        $shipment->setTrackingLink($trackingLink);

        if (!empty($response)
            && !empty($response[0])
        ) {
            if (!empty($response[0]['href'])) {
                $shipment->setHref($response[0]['href']);
            }
            if (!empty($response[0]['id'])) {
                $shipment->setShipmentId($response[0]['id']);
            }
            if (!empty($response[0]['status'])) {
                $shipment->setStatus($response[0]['status']);
            }
            if (!empty($response[0]['parcels'][0]['parcelNo'])) {
                $shipment->setShipmentNo($response[0]['parcels'][0]['parcelNo']);
            }
            if (!empty($response[0]['orderNo'])) {
                $shipment->setOrderNo($response[0]['orderNo']);
            }
            if (!empty($response[0]['reference'])) {
                $shipment->setReference($response[0]['reference']);
            }
            if (!empty($response[0]['serviceId'])) {
                $shipment->setServiceId($response[0]['serviceId']);
            }
            if (!empty($response[0]['parcelCount'])) {
                $shipment->setParcelCount($response[0]['parcelCount']);
            }
            if (!empty($response[0]['sndName'])) {
                $shipment->setSndName($this->_convertToUtf8($response[0]['sndName']));
            }
            if (!empty($response[0]['sndZipcode'])) {
                $shipment->setSndZipcode($response[0]['sndZipcode']);
            }
            if (!empty($response[0]['sndCity'])) {
                $shipment->setSndCity($this->_convertToUtf8($response[0]['sndCity']));
            }
            if (!empty($response[0]['sndCountry'])) {
                $shipment->setSndCountry($response[0]['sndCountry']);
            }
            if (!empty($response[0]['rcvName'])) {
                $shipment->setRcvName($this->_convertToUtf8($response[0]['rcvName']));
            }
            if (!empty($response[0]['rcvZipcode'])) {
                $shipment->setRcvZipcode($response[0]['rcvZipcode']);
            }
            if (!empty($response[0]['rcvCity'])) {
                $shipment->setRcvCity($this->_convertToUtf8($response[0]['rcvCity']));
            }
            if (!empty($response[0]['rcvCountry'])) {
                $shipment->setRcvCountry($response[0]['rcvCountry']);
            }
            if (!empty($response[0]['created'])) {
                $shipment->setCreated($response[0]['created']);
            }
            if (!empty($response[0]['changed'])) {
                $shipment->setChanged($response[0]['changed']);
            }
            if (!empty($response[0]['shipDate'])) {
                $shipment->setShipDate($response[0]['shipDate']);
            }
            if (!empty($response[0]['returnShipment'])) {
                $shipment->setReturnShipment($response[0]['returnShipment']);
            }
            if (!empty($response[0]['normalShipment'])) {
                $shipment->setNormalShipment($response[0]['normalShipment']);
            }
            if (!empty($response[0]['consolidated'])) {
                $shipment->setConsolidated($response[0]['consolidated']);
            }
            if (!empty($response[0]['parcels'])) {
                $shipment->setParcels(json_encode($response[0]['parcels']));
            }
            if (!empty($response[0]['pdfs'])) {
                $shipment->setPdfs(json_encode($response[0]['pdfs']));
            }
            if (!empty($response[0]['previousPdfs'])) {
                $shipment->setPreviousPdfs(json_encode($response[0]['previousPdfs']));
            }
        }

        try {
            $shipment->save();
            $this->_logger->info(sprintf(
                'Shipment "%s"  saved successfully',
                $response[0]['parcels'][0]['parcelNo']
            ));
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
        }
    }

    /**
     * @param string $text
     * @return string
     * @todo Test if ÅÄÖ is preserved
     */
    private function _convertToUtf8($text)
    {
        return $text;
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }

}
