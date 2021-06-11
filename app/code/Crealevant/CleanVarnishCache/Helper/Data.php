<?php

namespace Crealevant\CleanVarnishCache\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @param $url
     */
    public function runCurl($url)
    {
        shell_exec('curl -I -X BAN ' . $url);
    }
}
