<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Helper\Library\CarrierServices;

/**
 *
 */
interface CarrierService
{

    /**
     * @param \Magento\Framework\DataObject $request
     * @param array $data
     * @param array $addons
     * @param \Magento\Framework\DataObject $order
     */
    public function apply($request, & $data, $addons, $order);
}
