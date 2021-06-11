<?php

namespace Crealevant\Unifaun\Block\Adminhtml\Shippingmethod;

class Form extends \Mediastrategi\Unifaun\Block\Adminhtml\Shippingmethod\Form
{
    protected function _prepareForm()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $title = (string)$this->getRequest()->getParam('title');
        $description = (string)$this->getRequest()->getParam('description');
        $store = (int)$this->getRequest()->getParam('store');
        $method = (string)$this->getRequest()->getParam('method');
        $active = (bool)$this->getRequest()->getParam('active');
        $storedShipments = (bool)$this->getRequest()->getParam('stored_shipment');
        $pickup = (string)$this->getRequest()->getParam('pickup');
        $automationEnable = (bool)$this->getRequest()->getParam('automation_enable');
        $automationPackageType = ($this->getRequest()->getParam('automation_package_type') ? (string)$this->getRequest()->getParam('automation_package_type') : 'PK');
        $automationOrderStatusBefore = ($this->getRequest()->getParam('automation_order_status_before') ? (string)$this->getRequest()->getParam('automation_order_status_before') : 'pending');
        $options = (array)$this->getRequest()->getParam('options');
        $extra = ($this->getRequest()->getParam('extra') && json_decode($this->getRequest()->getParam('extra'), true)
            ? json_decode($this->getRequest()->getParam('extra'), true)
            : []);
        $extraValid = empty($this->getRequest()->getParam('extra'))
            || (json_decode($this->getRequest()->getParam('extra'), true) !== null);
        $allAddons = (array)$this->getRequest()->getParam('addons');
        $addons = (isset($allAddons[$method]) ? (array)$allAddons[$method] : []);
        $image = $this->getRequest()->getParam('image');

        $model = $this->_shippingMethodFactory->create();
        $shippingMethod = $model->load($id);

        if ($shippingMethod->getData()) {
            $title = $shippingMethod->getData('title');
            $description = $shippingMethod->getData('description');
            $store = $shippingMethod->getData('store');
            $method = $shippingMethod->getData('method');
            $active = $shippingMethod->getData('active');
            $storedShipments = $shippingMethod->getData('stored_shipment');
            $pickup = $shippingMethod->getData('pickup');
            $automationEnable = $shippingMethod->getData('automation_enable');
            $automationPackageType = $shippingMethod->getData('automation_package_type');
            $automationOrderStatusBefore = $shippingMethod->getData('automation_order_status_before');
            $image = $shippingMethod->getData('image');
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
                    'action' => ($id ? $this->getUrl('/shippingmethod/edit/id/' . $id) : $this->getUrl('/shippingmethod/add')),
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

        //Add Image field
        $generalFieldset->addField(
            'description',
            'text',
            [
                'name' => 'description',
                'label' => __('Description'),
                'required' => false,
                'value' => $description
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
        /*
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
        */
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

        //Add Image field
        $generalFieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'required' => false,
                'value' => $image
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
        return \Magento\Backend\Block\Widget\Form\Generic::_prepareForm();
    }
}