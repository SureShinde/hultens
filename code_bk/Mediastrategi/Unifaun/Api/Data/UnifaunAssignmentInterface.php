<?php
/**
 *
 */
namespace Mediastrategi\Unifaun\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface UnifaunAssignmentInterface
 * @api
 * @since 2.3.2
 */
interface UnifaunAssignmentInterface extends ExtensibleDataInterface
{
    /**#@+
     * Unifaun assignment object data keys
     */
    const KEY_PICK_UP_LOCATION_ID = 'pick_up_location_id';
    const KEY_PICK_UP_LOCATION_NAME = 'pick_up_location_name';
    const KEY_PICK_UP_LOCATION_ADDRESS = 'pick_up_location_address1';
    const KEY_PICK_UP_LOCATION_ZIP_CODE = 'pick_up_location_zip_code';
    const KEY_PICK_UP_LOCATION_CITY = 'pick_up_location_city';
    const KEY_PICK_UP_LOCATION_COUNTRY = 'pick_up_location_country';
    /**#@-*/

    /**
     * Get pickup location id
     *
     * @return int
     * @since 2.3.2
     */
    public function getPickUpLocationId();

    /**
     * Set pickup location id
     *
     * @param int $pickUpLocationId
     * @return $this
     * @since 2.3.2
     */
    public function setPickUpLocationId($pickUpLocationId);

    /**
     * Get pick up location name
     *
     * @return string
     * @since 2.3.2
     */
    public function getPickUpLocationName();

    /**
     * Set pickup location name
     *
     * @param string $pickUpLocationName
     * @return $this
     * @since 2.3.2
     */
    public function setPickUpLocationName($pickUpLocationName);

    /**
     * Get pick up location address
     *
     * @return string
     * @since 2.3.2
     */
    public function getPickUpLocationAddress();

    /**
     * Set pickup location address
     *
     * @param string $pickUpLocationAddress
     * @return $this
     * @since 2.3.2
     */
    public function setPickUpLocationAddress($pickUpLocationAddress);

    /**
     * Get pick up location zip code
     *
     * @return string
     * @since 2.3.2
     */
    public function getPickUpLocationZipCode();

    /**
     * Set pickup location zip code
     *
     * @param string $pickUpLocationZipCode
     * @return $this
     * @since 2.3.2
     */
    public function setPickUpLocationZipCode($pickUpLocationZipCode);

    /**
     * Get pick up location city
     *
     * @return string
     * @since 2.3.2
     */
    public function getPickUpLocationCity();

    /**
     * Set pickup location city
     *
     * @param string $pickUpLocationCity
     * @return $this
     * @since 2.3.2
     */
    public function setPickUpLocationCity($pickUpLocationCity);

    /**
     * Get pick up location country
     *
     * @return string
     * @since 2.3.2
     */
    public function getPickUpLocationCountry();

    /**
     * Set pickup location country
     *
     * @param string $pickUpLocationCountry
     * @return $this
     * @since 2.3.2
     */
    public function setPickUpLocationCountry($pickUpLocationCountry);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Mediastrategi\Unifaun\Api\Data\UnifaunAssignmentExtensionInterface|null
     * @since 2.3.2
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Mediastrategi\Unifaun\Api\Data\UnifaunAssignmentExtensionInterface $extensionAttributes
     * @return $this
     * @since 2.3.2
     */
    public function setExtensionAttributes(
        \Mediastrategi\Unifaun\Api\Data\UnifaunAssignmentExtensionInterface $extensionAttributes
    );
}
