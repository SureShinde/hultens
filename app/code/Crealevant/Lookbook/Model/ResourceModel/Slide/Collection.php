<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Crealevant\Lookbook\Model\ResourceModel\Slide;

/**
 * Mmegamenu resource model collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Init resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Crealevant\Lookbook\Model\Slide', 'Crealevant\Lookbook\Model\ResourceModel\Slide');
    }
}
