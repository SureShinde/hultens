<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod;

/**
 *
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Mediastrategi\Unifaun\Model\ShippingmethodFactory
     */
    protected $_shippingMethodFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory
     */
    protected $_statusCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Mediastrategi\Unifaun\Helper\Data $helper
     * @param array [$data = array()]
     * @param \Mediastrategi\Unifaun\Model\ShippingmethodFactory $resourceModel
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Mediastrategi\Unifaun\Helper\Data $helper,
        array $data = [],
        \Mediastrategi\Unifaun\Model\ShippingmethodFactory $resourceModel,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
    ) {
        $this->_authSession = $authSession;
        $this->_helper = $helper;
        $this->_storeManager = $context->getStoreManager();
        $this->_shippingMethodFactory = $resourceModel;
        $this->_statusCollectionFactory = $statusCollectionFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );
    }

    /**
     *
     */
    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'save_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Save'),
                'data_attribute' => [
                    'role' => 'save',
                ],
                'class' => 'save primary',
                'onclick' => "jQuery('#msunifaun_shippingmethod_form').submit();",
            ]
        );
        return parent::_prepareLayout();
    }

    /**
     *
     */
    protected function _prepareForm()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $title = (string) $this->getRequest()->getParam('title');
        $store = (int) $this->getRequest()->getParam('store');
        $method = (string) $this->getRequest()->getParam('method');
        $active = (bool) $this->getRequest()->getParam('active');
        $storedShipments = (bool) $this->getRequest()->getParam('stored_shipment');
        $pickup = (string) $this->getRequest()->getParam('pickup');
        $automationEnable = (bool) $this->getRequest()->getParam('automation_enable');
        $automationPackageType = ($this->getRequest()->getParam('automation_package_type') ? (string) $this->getRequest()->getParam('automation_package_type') : 'PK');
        $automationOrderStatusBefore = ($this->getRequest()->getParam('automation_order_status_before') ? (string) $this->getRequest()->getParam('automation_order_status_before') : 'pending');
        $options = (array) $this->getRequest()->getParam('options');
        $extra = ($this->getRequest()->getParam('extra') && json_decode($this->getRequest()->getParam('extra'), true)
            ? json_decode($this->getRequest()->getParam('extra'), true)
            : []);
        $extraValid = empty($this->getRequest()->getParam('extra'))
            || (json_decode($this->getRequest()->getParam('extra'), true) !== null);
        $allAddons = (array) $this->getRequest()->getParam('addons');
        $addons = (isset($allAddons[$method]) ? (array) $allAddons[$method] : []);
        $customsEnabled = (bool) $this->getRequest()->getParam('customs_enabled');
        $customsDocuments = (array) $this->getRequest()->getParam('customs_documents');

        $model = $this->_shippingMethodFactory->create();
        $shippingMethod = $model->load($id);

        if ($shippingMethod->getData()) {
            $title = $shippingMethod->getData('title');
            $store = $shippingMethod->getData('store');
            $method = $shippingMethod->getData('method');
            $active = $shippingMethod->getData('active');
            $storedShipments = $shippingMethod->getData('stored_shipment');
            $pickup = $shippingMethod->getData('pickup');
            $automationEnable = $shippingMethod->getData('automation_enable');
            $automationPackageType = $shippingMethod->getData('automation_package_type');
            $automationOrderStatusBefore = $shippingMethod->getData('automation_order_status_before');
            $customsEnabled = $shippingMethod->getData('customs_enabled');
            $customsDocuments = ($shippingMethod->getData('customs_documents') ?
                json_decode($shippingMethod->getData('customs_documents'), true) :
                []);
            // die('documents: ' . print_r($customsDocuments, true));
            $addons = ($shippingMethod->getData('addons') ?
                json_decode($shippingMethod->getData('addons'), true) :
                []);
            $options = ($shippingMethod->getData('options') ?
                json_decode($shippingMethod->getData('options'), true) :
                []);
            $extra = ($shippingMethod->getData('extra') ?
                json_decode($shippingMethod->getData('extra'), true) :
                []);

            if (is_array($options)
                && !empty($options)
            ) {
                foreach ($options as &$option) {
                    if (!isset($option['title'])) {
                        $option['title'] = __('Untitled');
                    }
                    if (!isset($option['country'])) {
                        $option['country'] = '*';
                    }
                    if (!isset($option['zip'])) {
                        $option['zip'] = '*';
                    }
                    if (!isset($option['weight'])) {
                        $option['weight'] = '*';
                    }
                    if (!isset($option['width'])) {
                        $option['width'] = '*';
                    }
                    if (!isset($option['height'])) {
                        $option['height'] = '*';
                    }
                    if (!isset($option['depth'])) {
                        $option['depth'] = '*';
                    }
                    if (!isset($option['volume'])) {
                        $option['volume'] = '*';
                    }
                    if (!isset($option['cart_subtotal'])) {
                        $option['cart_subtotal'] = '*';
                    }
                    if (!isset($option['price'])) {
                        $option['price'] = '0';
                    }
                }
            }
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'msunifaun_shippingmethod_form',
                    'action' => ($id ? $this->getUrl('msunifaun/shippingmethod/edit/id/' . $id) : $this->getUrl('msunifaun/shippingmethod/add')),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $generalFieldset = $form->addFieldset(
            'general',
            [
                'legend' => __('General'),
                'class' => 'fieldset'
            ]
        );
        $generalFieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'required' => true,
                'value' => $title,
            ]
        );

        $stores = $this->_storeManager->getStores();
        $storesOptions = [];
        foreach ($stores as $storeItem) {
            $storesOptions[$storeItem->getId()] = $storeItem->getName();
        }
        unset($stores);

        $generalFieldset->addField(
            'store',
            'select',
            [
                'name' => 'store',
                'label' => __('Store View'),
                'required' => true,
                'options' => $storesOptions,
                'value' => $store,
            ]
        );
        $generalFieldset->addField(
            'method',
            'select',
            [
                'name' => 'method',
                'label' => __('Method'),
                'required' => true,
                'values' => $this->_helper->getCarrierServicesOptions(),
                'value' => $method,
            ]
        );
        $generalFieldset->addField(
            'active',
            'select',
            [
                'name' => 'active',
                'label' => __('Active'),
                'required' => true,
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'value' => $active,
            ]
        );
        $generalFieldset->addField(
            'stored_shipment',
            'select',
            [
                'name' => 'stored_shipment',
                'label' => __('Stored Shipments'),
                'required' => true,
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'value' => $storedShipments,
            ]
        );
        $generalFieldset->addField(
            'pickup',
            'select',
            [
                'name' => 'pickup',
                'label' => __('Custom Pick-Up Location'),
                'required' => true,
                'values' => $this->_helper->getCarriersWithPickUpLocationOptions(),
                'value' => $pickup,
            ]
        );

        $customsFieldset = $form->addFieldset(
            'customs',
            [
                'legend' => __('Customs'),
                'class' => 'fieldset'
            ]
        );
        $customsFieldset->addField(
            'customs_enabled',
            'select',
            [
                'name' => 'customs_enabled',
                'label' => __('Include Declaration'),
                'required' => true,
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'value' => $customsEnabled
            ]
        );
        $customsFieldset->addField(
            'customs_documents',
            'multiselect',
            [
                'canbeempty' => true,
                'name' => 'customs_documents',
                'label' => __('Documents'),
                'required' => false,
                'values' => [
                    [
                        'value' => 'proforma',
                        'label' => __('Commercial/proforma invoice')
                    ],
                    [
                        'value' => 'proformaplabedi',
                        'label' => __('Proforma invoice incl. EDI - PostNord only')
                    ],
                    [
                        'value' => 'plabedi',
                        'label' => __('Customs information via EDI only - PostNord only')
                    ],
                    [
                        'value' => 'edoc',
                        'label' => __('ED-document')
                    ],
                    [
                        'value' => 'cn22',
                        'label' => __('CN22')
                    ],
                    [
                        'value' => 'cn23',
                        'label' => __('CN23')
                    ],
                    [
                        'value' => 'proformaups',
                        'label' => __('Commercial invoice - UPS')
                    ],
                    [
                        'value' => 'upsedi',
                        'label' => __('Paperless invoice - UPS')
                    ],
                    [
                        'value' => 'upschild',
                        'label' => __('UPS World Ease Child shipment')
                    ],
                    [
                        'value' => 'proformatnt',
                        'label' => __('Proforma for TNT')
                    ],
                    [
                        'value' => 'notetnt',
                        'label' => __('TNT Note')
                    ],
                    [
                        'value' => 'pnlwaybilledi',
                        'label' => __('Separate custom docs for PNL are sent via EDI, not from this system')
                    ],
                    [
                        'value' => 'fedexp',
                        'label' => __('Commercial Invoice for FedEx')
                    ],
                    [
                        'value' => 'fedexe',
                        'label' => __('Electronic trade documents - ETD, for FedEx')
                    ],
                    [
                        'value' => 'tradeinvoicepdk',
                        'label' => __('PostNord DK commercial invoice')
                    ],
                    [
                        'value' => 'datatransferpdk',
                        'label' => __('Customs information via EDI only - PostNord DK only')
                    ],
                    [
                        'value' => 'security',
                        'label' => __('Security Declaration')
                    ]
                ],
                'value' => $customsDocuments
            ]
        );

        $automationFieldset = $form->addFieldset(
            'automation',
            [
                'legend' => __('Automation'),
                'class' => 'fieldset'
            ]
        );
        $automationFieldset->addField(
            'automation_enable',
            'select',
            [
                'name' => 'automation_enable',
                'label' => __('Enable'),
                'required' => true,
                'options' => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
                'value' => $automationEnable,
            ]
        );
        $automationFieldset->addField(
            'automation_package_type',
            'select',
            [
                'name' => 'automation_package_type',
                'label' => __('Package Type'),
                'required' => true,
                'options' => $this->_helper->getAllContainerTypes(),
                'value' => $automationPackageType,
            ]
        );
        $items = $this->_statusCollectionFactory->create()->toOptionArray();
        $orderStatuses = [];
        foreach ($items as $item) {
            $orderStatuses[$item['value']] = $item['label'];
        }
        $automationFieldset->addField(
            'automation_order_status_before',
            'select',
            [
                'name' => 'automation_order_status_before',
                'label' => __('Order Status'),
                'required' => true,
                'options' => $orderStatuses,
                'value' => $automationOrderStatusBefore,
            ]
        );

        $addonsFieldset = $form->addFieldset(
            'addons-fieldset',
            [
                'legend' => __('Addons'),
                'class' => 'fieldset'
            ]
        );
        $addonsFieldset->addType(
            'addons',
            '\Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod\Addons'
        );
        $addonsFieldset->addField(
            'addons',
            'addons',
            [
                'name' => 'addons',
                'label' => __('Addons'),
                'title' => __('Addons'),
                'value' => $addons,
                'options' => $this->_helper->getServiceAddons(),
                'dynamic_fields' => $this->_helper->getDynamicFields(),
                'pickup_locations' => $this->_helper->getCarriersWithPickUpLocation(),
            ]
        );

        $specificationFieldset = $form->addFieldset(
            'specification',
            [
                'legend' => __('Specification'),
                'class' => 'fieldset'
            ]
        );
        $specificationFieldset->addType(
            'options',
            '\Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod\Options'
        );
        $specificationFieldset->addField(
            'options',
            'options',
            [
                'name' => 'options',
                'label' => __('Options'),
                'title' => __('Options'),
                'value' => $options,
            ]
        );

        $extraFieldset = $form->addFieldset(
            'extra-fieldset',
            [
                'legend' => __('Extra'),
                'class' => 'fieldset'
            ]
        );
        $extraFieldset->addType(
            'extra',
            '\Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod\Extra'
        );
        $extraFieldset->addField(
            'extra',
            'extra',
            [
                'name' => 'extra',
                'label' => __('Extra'),
                'title' => __('Extra'),
                'value' => $extra,
                'error' => !$extraValid,
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
