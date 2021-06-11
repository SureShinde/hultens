<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Ui\Component\Listing\Column;

/**
 *
 */
class ShippingmethodStore extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     *
     */
    protected $_storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     * @param \Magento\Backend\Block\Template\Context $templateContext
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [],
        \Magento\Backend\Block\Template\Context $templateContext
    ) {
    
        $this->_storeManager = $templateContext->getStoreManager();
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $stores = $this->_storeManager->getStores();
        $storesOptions = [];
        foreach ($stores as $storeItem) {
            $storesOptions[$storeItem->getId()] = $storeItem->getName();
        }
        unset($stores);
        if (!empty($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($storesOptions[$item['store']])) {
                    $item['store_id'] = $item['store'];
                    $item['store'] = $storesOptions[$item['store']];
                }
            }
        }
        return $dataSource;
    }
}
