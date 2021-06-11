<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Block\System\Config\Form\Field;

/**
 *
 */
class Status extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    private $regionFactory;

    /**
     * @var \Mediastrategi\Unifaun\Helper\Data
     */
    private $helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Mediastrategi\Unifaun\Helper\Data $helper
    ) {
        $this->authSession = $authSession;
        $this->regionFactory = $regionFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        $html = '';
        $storeId = $this->getRequest()->getParam('store');

        $shipperRegionCode = $this->helper->getStoreConfig(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_REGION_ID,
            $storeId
        );
        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = $this->regionFactory->create()->load($shipperRegionCode)->getCode();
        }

        $originStreet1 = $this->helper->getStoreConfig(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS1,
            $storeId
        );
        $storeInfo = new \Magento\Framework\DataObject(
            (array) $this->helper->getStoreConfig(
                'general/store_information',
                $storeId
            )
        );

        $errors = [];

        if (!$this->helper->getStoreConfig(
            'carriers/msunifaun/credentials/username',
            $storeId
        )) {
            $errors[] = __('API Key Id');
        }
        if (!$this->helper->getStoreConfig(
            'carriers/msunifaun/credentials/password',
            $storeId
        )) {
            $errors[] = __('API Key Secret');
        }
        if (!$this->helper->getStoreConfig(
            'carriers/msunifaun/credentials/user_id',
            $storeId
        )) {
            $errors[] = __('User Id');
        }
        if (!$this->helper->getStoreConfig(
            'carriers/msunifaun/credentials/quick_id',
            $storeId
        )) {
            $errors[] = __('Quick Id');
        }
        $admin = $this->authSession->getUser();
        if (!$admin->getFirstname()) {
            $errors[] = __('Admin Firstname');
        }
        if (!$admin->getLastname()) {
            $errors[] = __('Admin Lastname');
        }
        if (!$storeInfo->getName()) {
            $errors[] = __('Store Name');
        }
        if (!$storeInfo->getPhone()) {
            $errors[] = __('Store Phone');
        }
        if (!$originStreet1) {
            $errors[] = __('Origin Street 1');
        }
        if (!$shipperRegionCode) {
            $errors[] = __('Shipper Region Code');
        }
        if (!$this->helper->getStoreConfig(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
            $storeId
        )) {
            $errors[] = __('Store City');
        }
        if (!$this->helper->getStoreConfig(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
            $storeId
        )) {
            $errors[] = __('Store ZIP/Postcode');
        }
        if (!$this->helper->getStoreConfig(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
            $storeId
        )) {
            $errors[] = __('Store Country ID');
        }

        if ($errors) {
            $html .= '<strong>' . __('Errors - Missing Information:')
                  . '</strong><ul style="'
                  . 'margin: 10px 0; list-style: inside circle none; color: red; font-weight: bold;"><li>'
                  . implode('</li><li>', $errors) . '</li></ul>';
        } else {
            $html .= '<strong style="color: green; font-weight: bold;">' . __('Site Configuration OK') . '</strong>';
        }

        return $html;
    }
}
