<?php
namespace Crealevant\Hultencheckout\Model;

use Crealevant\Hultencheckout\Api\Data\ItemUpdateInterface;

class ItemUpdate implements ItemUpdateInterface
{
    protected $itemId;

    protected $qty;

    public function getItemId()
    {
        return $this->itemId;
    }

    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    public function getQty()
    {
        return $this->qty;
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
    }
}