<?php

namespace Crealevant\CleanVarnishCache\Block\Adminhtml\Grid\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;
use Magento\Backend\Model\Auth\Session;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;

    protected $_adminSession;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Session $adminSession,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_adminSession = $adminSession;
    }


    protected function _prepareForm()
    {
        $isElementDisabled = false;

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Clean Varnish Cache')]);

        $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('Url'),
                'title' => __('Url'),
                'required' => true,
                'value' => null,
                'disabled' => $isElementDisabled
            ]
        );

        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Clean Vanish Cache');
    }


    public function getTabTitle()
    {
        return __('Clean Vanish Cache');
    }


    public function canShowTab()
    {
        return true;
    }


    public function isHidden()
    {
        return false;
    }


    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}