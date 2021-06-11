<?php
namespace Mediastrategi\Unifaun\Model\Order;

use Magento\Framework\Model\AbstractExtensibleModel;
use Mediastrategi\Unifaun\Api\Data\UnifaunAssignmentInterface;

class UnifaunAssignment extends AbstractExtensibleModel implements UnifaunAssignmentInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPickUpLocationId()
    {
        return $this->getData(self::KEY_PICK_UP_LOCATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPickUpLocationId($pickUpLocationId)
    {
        return $this->setData(self::KEY_PICK_UP_LOCATION_ID, $pickUpLocationId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPickUpLocationName()
    {
        return $this->getData(self::KEY_PICK_UP_LOCATION_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setPickUpLocationName($pickUpLocationName)
    {
        return $this->setData(self::KEY_PICK_UP_LOCATION_NAME, $pickUpLocationName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPickUpLocationAddress()
    {
        return $this->getData(self::KEY_PICK_UP_LOCATION_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPickUpLocationAddress($pickUpLocationAddress)
    {
        return $this->setData(self::KEY_PICK_UP_LOCATION_ADDRESS, $pickUpLocationAddress);
    }

    /**
     * {@inheritdoc}
     */
    public function getPickUpLocationZipCode()
    {
        return $this->getData(self::KEY_PICK_UP_LOCATION_ZIP_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPickUpLocationZipCode($pickUpLocationZipCode)
    {
        return $this->setData(self::KEY_PICK_UP_LOCATION_ZIP_CODE, $pickUpLocationZipCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getPickUpLocationCity()
    {
        return $this->getData(self::KEY_PICK_UP_LOCATION_CITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setPickUpLocationCity($pickUpLocationCity)
    {
        return $this->setData(self::KEY_PICK_UP_LOCATION_CITY, $pickUpLocationCity);
    }

    /**
     * {@inheritdoc}
     */
    public function getPickUpLocationCountry()
    {
        return $this->getData(self::KEY_PICK_UP_LOCATION_COUNTRY);
    }

    /**
     * {@inheritdoc}
     */
    public function setPickUpLocationCountry($pickUpLocationCountry)
    {
        return $this->setData(self::KEY_PICK_UP_LOCATION_COUNTRY, $pickUpLocationCountry);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Mediastrategi\Unifaun\Api\Data\UnifaunAssignmentExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

}
