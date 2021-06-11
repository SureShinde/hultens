<?php


namespace Crealevant\FeedManager\Model;

use Crealevant\FeedManager\Api\Data\FeedsInterface;

class Feeds extends \Magento\Framework\Model\AbstractModel implements FeedsInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Crealevant\FeedManager\Model\ResourceModel\Feeds');
    }

    /**
     * Get id
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param string $id
     * @return \Crealevant\FeedManager\Api\Data\FeedsInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Crealevant\FeedManager\Api\Data\FeedsInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get path
     * @return string
     */
    public function getPath()
    {
        return $this->getData(self::PATH);
    }

    /**
     * Set path
     * @param string $path
     * @return \Crealevant\FeedManager\Api\Data\FeedsInterface
     */
    public function setPath($path)
    {
        return $this->setData(self::PATH, $path);
    }
}
