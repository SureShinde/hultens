<?php
/**
 *
 */

namespace Mediastrategi\Unifaun\Model\Carrier;

use Magento\Framework\DataObject;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 *
 */
class Shippingmethod extends \Magento\Shipping\Model\Carrier\AbstractCarrierOnline implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data
     */
    protected $_helper;

    /**
     * Carrier's code
     *
     * @var string
     */
    protected $_code = 'msunifaun';

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @var bool
     */
    protected $_isFixed = false;

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Mediastrategi\Unifaun\Model\ShippingmethodFactory
     */
    protected $_shippingMethodFactory;

    /**
     * @var \Mediastrategi\Unifaun\Model\ShipmentFactory
     */
    protected $_shipmentFactory;

    /**
     * \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Shipping\Model\Tracking\ResultFactory
     */
    protected $_result;

    /**
     * @param string $message
     */
    public function log($message)
    {
        if ($this->_helper->getDebugStatus()) {
            $this->_logger->debug(
                date('Y-m-d H:i:s') . ' - ' . $message . "\n"
            );
        }
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param \Magento\Framework\DataObject $request
     * @return \Magento\Framework\DataObject
     */
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $this->log(__METHOD__ . ' request: ' . print_r($request->debug(), true));
        return $this->_helper->createShipment($request);
    }

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Xml\Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingMethodFactory
     * @param \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Xml\Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingMethodFactory,
        \Mediastrategi\Unifaun\Model\ShipmentFactory $shipmentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Mediastrategi\Unifaun\Helper\Data $helper,
        array $data = []
    ) {
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_rateResultFactory = $rateFactory;
        $this->_localeFormat = $localeFormat;
        $this->_shippingMethodFactory = $shippingMethodFactory;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->_helper = $helper;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }

    /**
     * Do return of shipment
     *
     * @param Request $request
     * @return \Magento\Framework\DataObject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @todo Implement this
     */
    public function returnOfShipment($request)
    {
        $this->log(__METHOD__ . ' ' . print_r($request->debug(), true));
        return parent::returnOfShipment($request);
    }

    /**
     * Return container types of carrier
     *
     * @param \Magento\Framework\DataObject|null $params
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getContainerTypes(\Magento\Framework\DataObject $params = null)
    {
        $this->log(__METHOD__ . ' ' . print_r($params, true));
        return $this->_helper->getContainerTypes($params);
    }

    /**
     * @param \Magento\Framework\DataObject $request
     * @return $this|bool|false|\Magento\Framework\Model\AbstractModel
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function checkAvailableShipCountries(\Magento\Framework\DataObject $request)
    {
        $this->log(__METHOD__ . ' ' . $request->getDestCountryId());
        $currentStoreId = $this->_storeManager->getStore()->getId();
        $shippingMethod = $this->_shippingMethodFactory->create();
        $collection = $shippingMethod->getCollection();
        foreach ($collection as $item) {
            if ($item->getData('active')
                && (empty($item->getData('store'))
                    || $item->getData('store') == $currentStoreId)
            ) {
                $options = (array)json_decode($item->getData('options'), true);
                if (is_array($options)
                    && !empty($options)
                ) {
                    foreach ($options as $option) {
                        if (empty($option['country'])
                            || $option['country'] == '*'
                        ) {
                            $this->log('Country is allowed');
                            return $this;
                        } elseif ($option['country'] == $request->getDestCountryId()) {
                            $this->log('Country is allowed');
                            return $this;
                        } elseif (strpos($option['country'], ',') !== false) {
                            $countries = array_map('trim', explode(',', $option['country']));
                            if (in_array($request->getDestCountryId(), $countries)) {
                                $this->log('Country is allowed');
                                return $this;
                            }
                        }
                    }
                }
            }
        }

        return false; // Destination - country was not allowed
    }


    /**
     * Processing additional validation to check is carrier applicable.
     *
     * @param \Magento\Framework\DataObject $request
     * @return $this|bool|\Magento\Framework\DataObject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @todo Implement this
     */
    public function proccessAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        $this->log(__METHOD__ . ' ' . print_r($request->debug(), true));
        return $this;
    }

    /**
     * Alias to proccessAdditionalValidation
     * @param DataObject $request
     * @return bool|DataObject|Shippingmethod
     */
    public function processAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        return $this->proccessAdditionalValidation($request);
    }

    /**
     * Get Container Types, that could be customized
     *
     * @return string[]
     */
    public function getCustomizableContainerTypes()
    {
        $this->log(__METHOD__ . ' ' . print_r(func_get_args(), true));
        return [];
        $result = [];
        $containerTypes = $this->getContainerTypes();
        foreach (parent::getCustomizableContainerTypes() as $containerType) {
            $result[$containerType] = $containerTypes[$containerType];
        }
        return $result;
    }

    /**
     * Check if carrier has shipping tracking option available
     *
     * @return bool
     */
    public function isTrackingAvailable()
    {
        $this->log(__METHOD__ . ' ' . print_r(func_get_args(), true));
        return true;
    }

    /**
     * Check if carrier has shipping label option available
     *
     * @return bool
     */
    public function isShippingLabelsAvailable()
    {
        $this->log(__METHOD__ . ' ' . print_r(func_get_args(), true));
        return true;
    }

    /**
     * Generates list of allowed carrier`s shipping methods
     * Displays on cart price rules page
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $this->log(__METHOD__ . print_r(func_get_args(), true));
        $shippingMethod = $this->_shippingMethodFactory->create();
        $collection = $shippingMethod->getCollection();
        $methods = [];
        foreach ($collection as $item) {
            $methods[$this->getCarrierCode() . '_' . $item->getData('id')] = $item->getData('title');
        }
        return $methods;
    }

    /**
     * @static
     * @param float $value
     * @param string $span
     * @return bool
     */
    private function _valueIsInSpan($value, $span)
    {
        if (empty($span)
            || $span == '*'
        ) {
            return true;
        }
        if (strpos($span, '-') === false) {
            if ($value == $span) {
                return true;
            }
        } else {
            $parts = explode('-', $span);
            $min = trim($parts[0]);
            $max = trim($parts[1]);
            if ((empty($min)
                || $value >= $min)
                && (empty($max)
                || $value < $max)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sets the number of boxes for shipping
     *
     * @param int $weight in some measure
     * @return int
     */
    public function getTotalNumOfBoxes($weight)
    {
        $this->log(__METHOD__ . ' ' . $weight);
        /*
        reset num box first before retrieve again
        */
        $this->_numBoxes = 1;
        $maxPackageWeight = $this->getConfigData('max_package_weight');
        if ($weight > $maxPackageWeight && $maxPackageWeight != 0) {
            $this->_numBoxes = ceil($weight / $maxPackageWeight);
            $weight = $weight / $this->_numBoxes;
        }

        return $weight;
    }

    /**
     * Collect and get rates for storefront
     *
     * @param RateRequest $request
     * @return DataObject|bool|null
     */
    public function collectRates(RateRequest $request)
    {
        $this->log(__METHOD__ . ' ' . print_r($request->debug(), true));

        // Make sure that Shipping method is enabled
        if (!$this->_helper->isEnabled()) {
            return false;
        }

        $this->addMissingRequestPackageFields($request);
        $currentStoreId = $this->_storeManager->getStore()->getId();

        $freeBoxes = $this->getFreeBoxesCount($request);
        $this->setFreeBoxes($freeBoxes);
        $useFreeShipping = ($request->getFreeShipping() === true);
        $packageQuantity = $request->getPackageQty();

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $matchesCount = 0;
        $shippingMethod = $this->_shippingMethodFactory->create();
        $collection = $shippingMethod->getCollection();
        foreach ($collection as $item) {
            if ($item->getData('active')
                && (empty($item->getData('store'))
                    || $item->getData('store') == $currentStoreId)
            ) {
                $options = (array)json_decode($item->getData('options'), true);

                $lowestPrice = null;
                $lowestPriceTitle = null;
                $foundMatching = false;

                if (is_array($options)
                    && !empty($options)
                ) {
                    foreach ($options as $option) {
                        $countryMatching = false;
                        $zipMatching = false;
                        $weightMatching = false;
                        $heightMatching = false;
                        $widthMatching = false;
                        $depthMatching = false;
                        $volumeMatching = false;
                        $cartSubtotalMatching = false;

                        if (empty($option['country'])
                            || $option['country'] == '*'
                            || $option['country'] == $request->getDestCountryId()
                        ) {
                            $countryMatching = true;
                        } elseif (strpos($option['country'], ',') !== false) {
                            $countries = array_map('trim', explode(',', $option['country']));
                            if (in_array($request->getDestCountryId(), $countries)) {
                                $countryMatching = true;
                            }
                        }

                        if ($this->_valueIsInSpan(
                            str_replace(' ', '', $request->getDestPostcode()),
                            (isset($option['zip']) ? $option['zip'] : '')
                        )) {
                            $zipMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getPackageWeight(),
                            (isset($option['weight']) ? $option['weight'] : '')
                        )) {
                            $weightMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getPackageHeight(),
                            (isset($option['height']) ? $option['height'] : '')
                        )) {
                            $heightMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getPackageWidth(),
                            (isset($option['width']) ? $option['width'] : '')
                        )) {
                            $widthMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getPackageDepth(),
                            (isset($option['depth']) ? $option['depth'] : '')
                        )) {
                            $depthMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            ($request->getPackageHeight() * $request->getPackageWidth() * $request->getPackageDepth()),
                            (isset($option['volume']) ? $option['volume'] : '')
                        )) {
                            $volumeMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getBaseSubtotalInclTax(),
                            (isset($option['cart_subtotal']) ? $option['cart_subtotal'] : '')
                        )) {
                            $cartSubtotalMatching = true;
                        }

                        if ($countryMatching
                            && $zipMatching
                            && $weightMatching
                            && $heightMatching
                            && $widthMatching
                            && $depthMatching
                            && $volumeMatching
                            && $cartSubtotalMatching
                        ) {
                            if ($useFreeShipping
                                || $freeBoxes == $packageQuantity
                            ) {
                                $price = 0.00;
                            } else {
                                $price = (!empty($option['price']) ? (float)$option['price'] : 0.0);
                            }

                            if (!isset($lowestPrice)
                                || $price < $lowestPrice
                            ) {
                                $lowestPrice = $price;
                                $lowestPriceTitle = $option['title'];
                            }
                            $foundMatching = true;
                        }
                    }

                    if ($foundMatching) {
                        $method = $this->_rateMethodFactory->create();
                        $method->setCarrier($this->getCarrierCode());
                        $method->setCarrierTitle($item->getData('title'));
                        $method->setMethod($this->getCarrierCode() . '_' . $item->getData('id'));
                        $method->setMethodTitle($lowestPriceTitle);
                        $method->setPrice($lowestPrice);
                        $method->setCost($lowestPrice);
                        $pickup = $item->getData('pickup');

                        if (!empty($pickup)) {
                            if ($agents = $this->_helper->getPickUpLocations(
                                $request->getDestCountryId(),
                                $pickup,
                                str_replace(' ', '', $request->getDestPostcode()))
                            ) {
                                $method->setData(
                                    'msunifaun_agents',
                                    json_encode($agents)
                                );
                            }
                        }
                        $method->setData(
                            'msunifaun_carrier',
                            $this->_helper->getCarrierByService(
                                $item->getData('method')
                            )
                        );

                        $result->append($method);
                        $matchesCount++;
                    }
                }
            }
        }

        $this->log(__(
            'Found %1 matching rates',
            $matchesCount
        ));
        return $result;
    }

    /**
     * @internal
     * @param mixed $item
     * @return mixed
     */
    private function getFreeBoxesCountFromChildren($item)
    {
        $freeBoxes = 0;
        foreach ($item->getChildren() as $child) {
            if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                $freeBoxes += $item->getQty() * $child->getQty();
            }
        }
        return $freeBoxes;
    }

    /**
     * @internal
     * @param RateRequest $request
     * @return int
     */
    private function getFreeBoxesCount(RateRequest $request)
    {
        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    $freeBoxes += $this->getFreeBoxesCountFromChildren($item);
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        return $freeBoxes;
    }

    /**
     * If package object lacks height, width or length / depth then we add them to the object.
     * We add the maximum values of each.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     */
    public function addMissingRequestPackageFields(& $request)
    {
        $packageHeight = 0.0;
        $packageWidth = 0.0;
        $packageDepth = 0.0;
        foreach ($request->getAllItems() as $item) {
            if ($itemProduct = $item->getProduct()) {
                try {
                    $product = $this->productRepository->getById($itemProduct->getId());
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $product = false;
                }
                if (!empty($product)) {
                    if (!$productHeight = $product->getData('height')) {
                        $productHeight = $product->getData('ts_dimensions_height');
                    }
                    if ($productHeight
                        && $productHeight > $packageHeight
                    ) {
                        $packageHeight = (float) $productHeight;
                    }

                    if (!$productWidth = $product->getData('width')) {
                        $productWidth = $product->getData('ts_dimensions_width');
                    }
                    if ($productWidth
                        && $productWidth > $packageWidth
                    ) {
                        $packageWidth = (float) $productWidth;
                    }

                    if (!$productDepth = $product->getData('depth')) {
                        if (!$productDepth = $product->getData('length')) {
                            $productDepth = $product->getData('ts_dimensions_length');
                        }
                    }
                    if ($productDepth
                        && $productDepth > $packageDepth
                    ) {
                        $packageDepth = (float) $productDepth;
                    }
                }
            }
        }

        // Add request package height if it's missing
        if (!$request->getPackageHeight()) {
            $request->setPackageHeight($packageHeight);
        }

        // Add request package width if it's missing
        if (!$request->getPackageWidth()) {
            $request->setPackageWidth($packageWidth);
        }

        // Add request package depth if it's missing
        if (!$request->getPackageDepth()) {
            $request->setPackageDepth($packageDepth);
        }

        /* die(sprintf(
        'Height: %s, width: %s, depth: %s, package: %s',
        $request->getPackageHeight(),
        $request->getPackageWidth(),
        $request->getPackageDepth(),
        print_r($request->debug(), true)
        )); */
    }

    /**
     * Get tracking
     *
     * @param string|string[] $trackings
     * @return Result
     */
    public function getTracking($trackings)
    {
        $this->log(__METHOD__ . ' $trackings: ' . print_r($trackings, true));
        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }
        $this->_getUnifaunTracking($trackings);
        return $this->_result;
    }

    /**
     * Get Unifaun tracking
     *
     * @internal
     * @param string[] $trackings
     * @return \Magento\Shipping\Model\Tracking\ResultFactory
     */
    protected function _getUnifaunTracking($trackings)
    {
        $this->log(__METHOD__ . ' trackings: ' . print_r($trackings, true));
        $userId = $this->getConfigData('credentials/user_id');
        $result = $this->_trackFactory->create();
        $shipmentModel = $this->_shipmentFactory->create();
        foreach ($trackings as $tracking) {
            $shipment = $shipmentModel->loadByShipmentId($tracking);

            if (!empty($shipment->getData())) {
                $parcels = json_decode($shipment->getParcels());
                $this->log('$shipment: ' . print_r($shipment->getData(), true) . ', $parcels: ' . print_r($parcels, true));
                /** @var \stdClass $parcel */
                $tracking_url = $this->_helper->getShipmentTrackingLink(
                    $shipment,
                    $userId
                );
                $status = $this->_trackStatusFactory->create();
                $status->setCarrier($shipment->getPartnerId());
                $status->setCarrierTitle($this->_getCarrierTitle($shipment->getPartnerId()));
                $status->setTracking($tracking);
                $status->setPopup(1);
                $status->setUrl($tracking_url);
                $result->append($status);
            } else {
                $error = $this->_trackErrorFactory->create();
                $error->setTracking($tracking);
                $error->setErrorMessage(__(
                    "Shipment with tracking number '%1' couldn't be found",
                    $tracking
                ));
                $result->append($error);
            }
        }

        $this->_result = $result;
        return $result;
    }

    /**
     * @internal
     * @param int $carrierId
     * @return string
     */
    private function _getCarrierTitle($carrierId)
    {
        $model = $this->_shippingMethodFactory->create();
        $shippingMethod = $model->load($carrierId);
        return $shippingMethod->getData('title');
    }
}
