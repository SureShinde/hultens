<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod;

/**
 *
 */
class Automation extends \Magento\Backend\Block\Template
{

    /**
     * @var \Mediastrategi\Unifaun\Model\ShippingmethodFactory
     */
    protected $_shippingMethodFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_orderModel;

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $_shipmentLoader;

    /**
     * @var \Magento\Shipping\Model\Shipping\LabelGenerator
     */
    protected $_labelGenerator;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\ShipmentSender
     */
    protected $_shipmentSender;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_dbTransaction;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    protected $_orderConverter;

    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    protected $_shipmentNotifier;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieReaderInterface
     */
    protected $_cookieReaderInterface;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $_stringUtils;

    /**
     * @var \Magento\Framework\App\Route\ConfigInterface\Proxy
     */
    protected $_configInterfaceProxy;

    /**
     * @var \Magento\Framework\App\Request\PathInfoProcessorInterface
     */
    protected $_patInfoProcessorInterface;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManagerInterface;

    /**
     * @var \Magento\User\Model\User
     */
    protected $_userModel;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @internal
     * @var string
     */
    private $_log = '';

    /**
     * @internal
     * @var string
     */
    private $_debug = false;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    private $regionFactory;

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingMethodFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Sales\Model\Order $orderModel
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator
     * @param \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
     * @param \Magento\Framework\DB\Transaction $dbTransation
     * @param \Magento\Sales\Model\Convert\Order $orderConverter
     * @param \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
     * @param \Magento\Framework\Stdlib\Cookie\CookieReaderInterface $cookieReaderInterface
     * @param \Magento\Framework\Stdlib\StringUtils $stringUtils
     * @param \Magento\Framework\App\Request\PathInfoProcessorInterface $patInfoProcessorInterface
     * @param \Magento\Framework\ObjectManagerInterface $objectManagerInterface
     * @param \Magento\User\Model\User $userModel
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Mediastrategi\Unifaun\Helper\Data $dataHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array [$data = array()]
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mediastrategi\Unifaun\Model\ShippingmethodFactory $shippingMethodFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \Magento\Framework\DB\Transaction $dbTransaction,
        \Magento\Sales\Model\Convert\Order $orderConverter,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Framework\Stdlib\Cookie\CookieReaderInterface $cookieReaderInterface,
        \Magento\Framework\Stdlib\StringUtils $stringUtils,
        \Magento\Framework\App\Request\PathInfoProcessorInterface $patInfoProcessorInterface,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magento\User\Model\User $userModel,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Mediastrategi\Unifaun\Helper\Data $dataHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $data = []
    ) {
        $this->_shippingMethodFactory = $shippingMethodFactory;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_orderModel = $orderModel;
        $this->_shipmentLoader = $shipmentLoader;
        $this->_labelGenerator = $labelGenerator;
        $this->_shipmentSender = $shipmentSender;
        $this->_dbTransaction = $dbTransaction;
        $this->_orderConverter = $orderConverter;
        $this->_shipmentNotifier = $shipmentNotifier;
        $this->_cookieReaderInterface = $cookieReaderInterface;
        $this->_stringUtils = $stringUtils;
        $this->_configInterfaceProxy = new \Magento\Framework\App\Route\ConfigInterface\Proxy(
            $objectManager,
            \Magento\Framework\App\Route\ConfigInterface::class,
            true
        );
        $this->_pathInfoProcessorInterface = $patInfoProcessorInterface;
        $this->_objectManagerInterface = $objectManagerInterface;
        $this->_userModel = $userModel;
        $this->_authSession = $authSession;
        $this->_storeManager = $storeManager;
        $this->regionFactory = $regionFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getLog()
    {
        return $this->_log;
    }

    /**
     *
     */
    public function execute()
    {
        $this->_log = '';
        $this->_log(__('Automation started'));

        $successCount = 0;
        $errorCount = 0;

        try {

            // Iterate through shipping methods here
            $shippingMethods = $this->_shippingMethodFactory->create();
            foreach ($shippingMethods->getCollection() as $shippingMethod) {
                if ($shippingMethod->getData('automation_enable')) {
                    $status = $shippingMethod->getData('automation_order_status_before');
                    $criteria = $this->_searchCriteriaBuilder
                        ->addFilter('status', $status)
                        ->addFilter('shipping_method', 'msunifaun_msunifaun_' . $shippingMethod->getId())
                        ->create();
                    if ($orders = $this->_orderRepository->getList($criteria)->getItems()) {
                        foreach ($orders as $order) {
                            if ($order->canShip()) {
                                $this->_log(__(
                                    'Should automatically ship order %1..',
                                    $order->getId()
                                ));

                                $shipment = $this->_orderConverter->toShipment($order);

                                // Loop through order items
                                $packageItems = [];
                                $weight = 0;
                                $length = 0;
                                $width = 0;
                                $height = 0;
                                $customsValue = 0;
                                $validOrderItems = 0;
                                foreach ($order->getAllItems() as $orderItem) {

                                    // Check if order item has qty to ship or is virtual
                                    if (!$orderItem->getQtyToShip()
                                        || $orderItem->getIsVirtual()
                                    ) {
                                        continue;
                                    }
                                    $validOrderItems++;

                                    // echo 'order-item: <pre>' . print_r($orderItem->debug(), true) . '</pre>';
                                    $customsValue += $orderItem->getRowTotal();
                                    $weight += $orderItem->getRowWeight();

                                    // TODO: Sum length, width, height here too?

                                    $packageItems[] = [
                                        'qty' => $orderItem->getQtyToShip(),
                                        'customs_value' => $orderItem->getRowTotal(),
                                        'price' => $orderItem->getPrice(),
                                        'name' => $orderItem->getName(),
                                        'weight' => $orderItem->getRowWeight(),
                                        'product_id' => $orderItem->getProductId(),
                                        'order_item_id' => $orderItem->getItemId(),
                                    ];

                                    $qtyShipped = $orderItem->getQtyToShip();

                                    // Create shipment item with qty
                                    $shipmentItem = $this->_orderConverter->itemToShipmentItem($orderItem)->setQty($qtyShipped);

                                    // Add shipment item to shipment
                                    $shipment->addItem($shipmentItem);
                                }

                                $this->_log(__(
                                    'Added %1 valid order items to shipment',
                                    $validOrderItems
                                ));

                                // echo 'package-items: <pre>' . print_r($packageItems, true) . '</pre>';

                                // echo 'shipment: ' . print_r($shipment->debug(), true) . '</pre>';
                                if ($validOrderItems) {
                                    $packages = [
                                        'packages' => [
                                            [
                                                'params' => [
                                                    'container' => $shippingMethod->getData('automation_package_type'),
                                                    'weight' => ($weight ? $weight : 1),
                                                    'customs_value' => $customsValue,
                                                    'length' => ($length ? $length : ''),
                                                    'width' => ($width ? $width : ''),
                                                    'height' => ($height ? $height : ''),
                                                    'weight_units' => 'KILOGRAM',
                                                    'dimension_units' => 'METER',
                                                    'content_type' => '',
                                                    'content_type_other' => '',
                                                ],
                                                'items' => $packageItems,
                                            ]
                                        ]
                                    ];
                                    // echo 'packages: <pre>' . print_r($packages, true) . '</pre>';

                                    // Generate request here
                                    $request = new \Magento\Framework\App\Request\Http(
                                        $this->_cookieReaderInterface,
                                        $this->_stringUtils,
                                        $this->_configInterfaceProxy,
                                        $this->_pathInfoProcessorInterface,
                                        $this->_objectManagerInterface
                                    );
                                    $request->setParams($packages);
                                    $this->_log(__('Created shipment packages'));

                                    // Register shipment
                                    $shipment->register();
                                    $shipment->getOrder()->setIsInProcess(true);

                                    // Change store
                                    $oldStoreId = $this->_storeManager->getStore();
                                    $newStoreId = $shippingMethod->getData('store');
                                    $this->_log(__(
                                        'Setting current store to %1',
                                        $newStoreId
                                    ));
                                    $this->_storeManager->setCurrentStore($newStoreId);

                                    // Change session user
                                    $adminUsername = $shippingMethod->getData('automation_admin_username');
                                    $this->_log(__(
                                        'Loading username "%1"..',
                                        $adminUsername
                                    ));
                                    $user = $this->_userModel->loadByUsername($adminUsername);
                                    // echo 'user: <pre>' . print_r($user->debug(), true) . '</pre>';
                                    $this->_log(__(
                                        'Setting session to "%1"..',
                                        $adminUsername
                                    ));
                                    $oldUser = $this->_authSession->getUser();
                                    $this->_authSession->setUser($user);

                                    try {
                                        // Create shipping label
                                        $this->_log(__('Creating shipping labels..'));
                                        $this->_labelGenerator->create(
                                            $shipment,
                                            $request
                                        );
                                        $this->_log(__('Generated shipping labels'));

                                        // Revert session user again
                                        if ($oldUser) {
                                            $this->_log(__(
                                                'Reverting session user to "%1"..',
                                                $oldUser->getUserName()
                                            ));
                                            $this->_authSession->setUser($oldUser);
                                        }

                                        // Revert store again
                                        if ($oldStoreId
                                            && !is_object($oldStoreId)
                                        ) {
                                            $this->_log(__(
                                                'Reverting store to %1',
                                                $oldStoreId
                                            ));
                                            $this->_storeManager->setCurrentStore($oldStoreId);
                                        }

                                        // Save created shipment and order
                                        $shipment->save();
                                        $shipment->getOrder()->save();

                                        // Send notifications if enabled
                                        if ($this->dataHelper->getStoreConfig(
                                            'carriers/msunifaun/automatic_shipment_notifications',
                                            $newStoreId
                                        )) {
                                            // NOTE Send email, won't work until Magento 2.3, bug with looping and sending mails. Header already sent.
                                            $this->_shipmentNotifier->notify($shipment);
                                            $this->_log('Sent shipment notifications');
                                        } else {
                                            $this->_log('Did not send shipment notification');
                                        }

                                        $shipment->save();

                                        $this->_log(__(
                                            'Successfully shipped order %1',
                                            $order->getId()
                                        ));
                                        // echo 'shipment saved: <pre>' . print_r($shipment->debug(), true) . '</pre>';

                                        $successCount++;
                                    } catch (\Exception $e) {
                                        $this->_log(__(
                                            'Failed to ship order %1, error: "%2", trace: "%3"',
                                            $order->getId(),
                                            __($e->getMessage()),
                                            print_r($e->getTrace()[2], true)
                                        ));
                                        $errorCount++;
                                    }
                                } else {
                                    $this->_log(__(
                                        'Skipping order since we found no valid order items'
                                    ));
                                }
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->_log(__(
                'General error during automation, error: %1',
                $e->getMessage()
            ));
        }

        $this->_log(__(
            'Automation finished, successes: "%1", failures: "%2"',
            $successCount,
            $errorCount
        ));
    }

    /**
     * @internal
     * @param string $message
     */
    private function _log($message)
    {
        $line = date('Y-m-d H:i:s') . ' | ' . $message . "\n";
        $this->_log .= $line;
        if ($this->_debug) {
            file_put_contents(
                dirname(__FILE__) . '/cron-debug.log',
                $line,
                FILE_APPEND
            );
        }
    }
}
