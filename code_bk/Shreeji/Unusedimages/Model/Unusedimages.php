<?php

namespace Shreeji\Unusedimages\Model;

use Magento\Framework\Model\AbstractModel;

class Unusedimages extends AbstractModel {

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('Shreeji\Unusedimages\Model\ResourceModel\Unusedimages');
    }

}
