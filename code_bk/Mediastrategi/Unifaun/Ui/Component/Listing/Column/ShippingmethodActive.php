<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Ui\Component\Listing\Column;

/**
 *
 */
class ShippingmethodActive extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
    
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
        if (!empty($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['active'])) {
                    $item['active_id'] = $item['active'];
                    $item['active'] = (!empty($item['active']) ? __('Yes') : __('No'));
                }
            }
        }
        return $dataSource;
    }
}
