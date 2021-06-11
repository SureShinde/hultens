<?php


namespace Crealevant\FeedManager\Api\Data;

interface FeedsInterface
{

    const ID = 'id';
    const NAME = 'name';
    const PATH = 'path';

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Crealevant\FeedManager\Api\Data\FeedsInterface
     */
    public function setId($id);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Crealevant\FeedManager\Api\Data\FeedsInterface
     */
    public function setName($name);

    /**
     * Get path
     * @return string|null
     */
    public function getPath();

    /**
     * Set path
     * @param string $path
     * @return \Crealevant\FeedManager\Api\Data\FeedsInterface
     */
    public function setPath($path);
}
