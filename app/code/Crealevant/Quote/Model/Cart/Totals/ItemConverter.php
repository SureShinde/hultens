<?php
namespace Crealevant\Quote\Model\Cart\Totals;

use Magento\Quote\Model\Cart\Totals\ItemConverter as MagentoItemConverter;
use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Quote\Api\Data\TotalsItemInterfaceFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\Data\TotalsItemInterface;

class ItemConverter extends MagentoItemConverter
{
    /**
     * @var ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var \Magento\Quote\Api\Data\TotalsItemInterfaceFactory
     */
    private $totalsItemFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * Constructs a totals item converter object.
     *
     * @param ConfigurationPool $configurationPool
     * @param EventManager $eventManager
     * @param \Magento\Quote\Api\Data\TotalsItemInterfaceFactory $totalsItemFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @throws \RuntimeException
     */
    public function __construct(
        ConfigurationPool $configurationPool,
        EventManager $eventManager,
        TotalsItemInterfaceFactory $totalsItemFactory,
        DataObjectHelper $dataObjectHelper,
        Json $serializer = null
    ) {
        parent::__construct($configurationPool, $eventManager, $totalsItemFactory, $dataObjectHelper, $serializer);
        $this->configurationPool = $configurationPool;
        $this->eventManager = $eventManager;
        $this->totalsItemFactory = $totalsItemFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(Json::class);
    }

    public function modelToDataObject($item)
    {
        $this->eventManager->dispatch('items_additional_data', ['item' => $item]);
        $items = $item->toArray();
        $items['options'] = $this->getFormattedOptionValue($item);
        unset($items[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);

        $itemsData = $this->totalsItemFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $itemsData,
            $items,
            TotalsItemInterface::class
        );
        return $itemsData;
    }

    private function getFormattedOptionValue($item)
    {
        $optionsData = [];

        /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
        $helper = $this->configurationPool->getByProductType('default');

        $options = $this->configurationPool->getByProductType($item->getProductType())->getOptions($item);

        foreach ($options as $index => $optionValue) {
            $params = [
                'max_length' => 55,
                'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
            ];
            $option = $helper->getFormattedOptionValue($optionValue, $params);
            $optionsData[$index] = $option;
            $optionsData[$index]['label'] = $optionValue['label'];

            /*
             * Start Override
             * Resolve: custom thumbnail & delivery info children product bundle
             * */
            if(isset($optionValue['thumbnail'])){
                $optionsData[$index]['thumbnail'] = $optionValue['thumbnail'];
            }

            if(isset($optionValue['delivery_info'])){
                $optionsData[$index]['delivery_info'] = $optionValue['delivery_info'];
            }

            /* End Override*/
        }

        return $this->serializer->serialize($optionsData);
    }
}
