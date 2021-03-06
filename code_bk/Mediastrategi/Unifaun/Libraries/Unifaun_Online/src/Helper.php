<?php

/**
 *
 */
namespace Mediastrategi\UnifaunOnline
{

    /**
     *
     */
    class Helper
    {

        /**
         * @return array
         */
        public static function getAllContainerTypes()
        {
            $types = array();
            if ($carriersTypes = self::getCarrierContainerTypes()) {
                foreach ($carriersTypes as $carrierTypes) {
                    foreach ($carrierTypes as $code => $description) {
                        if (!isset($types[$code])) {
                            $types[$code] = $description;
                        }
                    }
                }
            }
            return $types;
        }

        /**
         * @static
         * @return array
         */
        public static function getShippingDocumentOptions()
        {
            return array(
                'label' => __um('parcel labels'),
                'dngdecl' => __um('dangerous goods declaration'),
                'sis' => __um('SIS/SFS/CMR waybill. Actual printed type depends on sender and receiver country.'),
                '*' => __um('None'),
            );
        }

        /**
         * @static
         * @return array
         */
        public static function getCustomDeclarationDocumentOptions()
        {
            return array(
                'proforma' => __um('Commercial/proforma invoice'),
                'proformaplabedi' => __um('Proforma invoice incl. EDI - PostNord only'),
                'plabedi' => __um('Customs information via EDI only - PostNord only'),
                'edoc' => __um('ED-document'),
                'cn22' => __um('CN22'),
                'cn23' => __um('CN23'),
                'proformaups' => __um('Commercial invoice - UPS'),
                'upsedi' => __um('Paperless invoice - UPS'),
                'upschild' => __um('UPS World Ease Child shipment'),
                'proformatnt' => __um('Proforma for TNT'),
                'notetnt' => __um('TNT Note'),
                'pnlwaybilledi' => __um('Separate custom docs for PNL are sent via EDI, not from this system'),
                'fedexp' => __um('commercial invoice for FedEx'),
                'fedexe' => __um('Electronic trade documents - ETD, for FedEx'),
                'tradeinvoicepdk' => __um('PostNord DK commercial invoice'),
                'datatransferpdk' => __um('Customs information via EDI only - PostNord DK only'),
                'security' => __um('Security Declaration'),
            );
        }

        /**
         * In format: array(carrier-id => array(container-type-code => container-type-description))
         *
         * @return array
         * @see https://www.unifaunonline.com/ Help > Code-lists
         */
        public static function getCarrierContainerTypes()
        {
            return require(self::_getLibraryPath('carrier-container-types.php'));
        }

        /**
         * @param string $service
         * @return array|bool
         */
        public static function getServiceAddons($service)
        {
            if ($serviceAddons = self::getServicesAddons()) {
                if (isset($serviceAddons[$service])) {
                    return $serviceAddons[$service];
                }
            }
            return false;
        }

        /**
         * @throws \Exception
         * @return array|bool
         */
        public static function getServicesAddons()
        {
            if ($carrierServices = self::getCarrierServices()) {
                $newServiceAddons = [];
                $universalAddons = [];
                $universalAddonsPath = self::_getLibraryPath('Addons/universal.php');
                if (file_exists($universalAddonsPath)) {
                    try {
                        if ($addons = require($universalAddonsPath)) {
                            $universalAddons = $addons;
                        }
                    } catch (\Exception $e) {
                        throw new \Exception(sprintf(
                            'Requiring "%s" generated error "%s"',
                            $universalAddonsPath,
                            $e->getMessage()
                        ));
                    }
                }
                foreach ($carrierServices as $carrierId => $carrier)
                {
                    if (isset($carrier['services'])) {
                        foreach (array_keys($carrier['services']) as $serviceId)
                        {
                            $path = self::_getLibraryPath('Addons/' . $carrierId . '/' . $serviceId . '.php');
                            $newServiceAddons[$serviceId] = $universalAddons;
                            if (file_exists($path)) {
                                try {
                                    if ($addons = require($path)) {
                                        $newServiceAddons[$serviceId] = array_merge(
                                            $newServiceAddons[$serviceId],
                                            $addons
                                        );
                                    }
                                } catch (\Exception $e) {
                                    throw new \Exception(sprintf(
                                        'Requiring "%s" generated error "%s"',
                                        $path,
                                        $e->getMessage()
                                    ));
                                }
                            }
                        }
                    }
                }
                if (count($newServiceAddons)) {
                    return $newServiceAddons;
                }
            }
            return false;
        }

        /**
         * @param string $service
         * @return array|bool
         */
        public static function getContainerTypesByService($service)
        {
            if ($carrier = self::getCarrierByService($service)) {
                return self::getContainerTypesByCarrier($carrier);
            }
            return false;
        }

        /**
         * @return array
         */
        public static function getCarriersWithPickUpLocation()
        {
            return [
                'BRING' => 'Bring',
                'BUSSGODS' => 'Bussgods',
                'DHLSP' => 'DHL ServicePoint',
                'DHLSPCOD' => 'DHL ServicePoint C.O.D',
                'DPD' => 'DPD',
                'GLS' => 'GLS',
                'ITELLA' => 'Posti',
                'ITELLASP' => 'Posti SmartPOST',
                'MHM' => 'Matkahuolto',
                'MHT' => 'Matkahuolto - Tradeka Retail Shops',
                'POSTI' => 'POSTI',
                'POSTNORD' => 'PostNord',
                'PP' => 'DB Schenker Privpak Sweden',
                'PPFI' => 'DB Schenker Privpak Finland',
                'SBTL' => 'DB Schenker Sweden',
                'SBTLFI' => 'DB Schenker Finland',
                'UPS' => 'UPS',
            ];
        }

        /**
         * @param string $carrier
         * @return array|bool
         */
        public static function getContainerTypesByCarrier($carrier)
        {
            $containerTypes = self::getCarrierContainerTypes();
            return (isset($containerTypes[$carrier]) ? $containerTypes[$carrier] : self::getAllContainerTypes());
        }

        /**
         * @param string $service
         * @return string|bool
         */
        public static function getCarrierByService($service)
        {
            foreach (self::getCarrierServices() as $carrierId => $carrier)
            {
                if (isset($carrier['services'][$service])) {
                    return $carrierId;
                }
            }
            return false;
        }

        /**
         * In format: carrier-id => array(service-code, ..)
         *
         * @return array
         * @see https://www.unifaunonline.com/ Help > Code-lists
         */
        public static function getCarrierServices()
        {
            return require(self::_getLibraryPath('carrier-services.php'));
        }

        /**
         * @param string $string
         */
        public static function __($string)
        {
            return $string;
        }

        /**
         * @param string [$append = '']
         * @return string
         */
        private static function _getLibraryPath($append = '')
        {
            return __DIR__ . '/Library/' . $append;
        }

    }
}
namespace {
    function __um($message)
    {
        return \Mediastrategi\UnifaunOnline\Helper::__($message);
    }
}
