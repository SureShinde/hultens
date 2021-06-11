<?php
namespace Crealevant\Hultencheckout\Api\Data;

interface ItemUpdateInterface
{
    /**
     *
     *
     * @return int
     */
    public function getItemId();

    /**
     *
     *
     * @param $itemId
     * @return int
     */
    public function setItemId($itemId);

    /**
     *
     *
     * @return int
     */
    public function getQty();

    /**
     *
     *
     * @param $qty
     * @return int
     */
    public function setQty($qty);
}