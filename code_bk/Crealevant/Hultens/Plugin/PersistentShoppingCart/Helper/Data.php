<?php
namespace Crealevant\Hultens\Plugin\PersistentShoppingCart\Helper;

class Data
{
    public function afterIsCookieRestricted(\TIG\PersistentShoppingCart\Helper\Data $subject, $result)
    {
        return false;
    }
}
