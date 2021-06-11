<?php

namespace Shreeji\Unusedimages\Model\ResourceModel\Unusedimages;

use \Shreeji\Unusedimages\Model\ResourceModel\AbstractCollection;

/**
 * ZIP code collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'unusedimage_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Shreeji\Unusedimages\Model\Unusedimages', 'Shreeji\Unusedimages\Model\ResourceModel\Unusedimages');        
    }    
}
