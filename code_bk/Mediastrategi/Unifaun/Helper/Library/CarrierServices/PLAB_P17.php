<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Helper\Library\CarrierServices;

/**
 *
 */
class PLAB_P17 implements CarrierService
{

    /**
     * @param \Magento\Framework\DataObject $request
     * @param array $data
     * @param array $addons
     * @param \Magento\Framework\DataObject $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @codingStandardsIgnoreStart
     */
    public function apply($request, & $data, $addons, $order)
    {
        /* @codingStandardsIgnoreEnd */
        // This is required for SMS
        if (!empty($data['shipment']['receiver']['phone'])
            && empty($data['shipment']['receiver']['mobile'])
        ) {
            $data['shipment']['receiver']['mobile'] =
                $data['shipment']['receiver']['phone'];
        }

        // Do we have a agent id specified?
        if (isset(
            $data,
            $data['shipment'],
            $data['shipment']['agent'],
            $data['shipment']['agent']['quickId']
            )
        ) {
            // Add PUPOPT to add-ons if it's missing
            $found = false;
            if (!empty($data['shipment']['service']['addons'])) {
                foreach ($data['shipment']['service']['addons'] as $addon) {
                    if (!empty($addon['id'])
                        && $addon['id'] == 'PUPOPT'
                    ) {
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                if (!isset($data['shipment']['service']['addons'])) {
                    $data['shipment']['service']['addons'] = array();
                }
                $data['shipment']['service']['addons'][] = [
                    'id' => 'PUPOPT',
                ];
            }

            // Is no agent specified but PUPOPT is specified?
        } else if (isset($data)
            && isset($data['shipment'])
            && isset($data['shipment']['service'])
            && isset($data['shipment']['service']['addons'])
            && is_array($data['shipment']['service']['addons'])
            && count($data['shipment']['service']['addons'])
        ) {
            // Remove PUPOPT from add-ons
            $newAddons = [];
            foreach ($data['shipment']['service']['addons'] as $addon) {
                if (empty($addon['id'])
                    || $addon['id'] != 'PUPOPT'
                ) {
                    $newAddons[] = $addon;
                }
            }
            $data['shipment']['service']['addons'] = $newAddons;
            if (!count($newAddons)) {
                unset($data['shipment']['service']['addons']);
            }
        }
    }
}
