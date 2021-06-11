<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Crealevant\ProductSlider\Model\ResourceModel\Bestsellers;

use Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection as BestsellersCollections;

/**
 * Class Collection
 * @package Crealevant\ProductSlider\Model\ResourceModel\Bestsellers
 */
class Collection extends BestsellersCollections
{
    protected $_ratingLimit = 5;

    public function setRatingLimit($ratingLimit)
    {
        $this->_ratingLimit = $ratingLimit;
        return  $this;
    }


}