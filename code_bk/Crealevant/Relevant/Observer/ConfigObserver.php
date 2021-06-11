<?php
namespace Crealevant\Relevant\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Psr\Log\LoggerInterface as Logger;

class ConfigObserver implements ObserverInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function execute(EventObserver $observer)
    {
        $maintenance = $this->scopeConfig->getValue('settings/maintenance/maintenance_field', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $maintenance_ip = $this->scopeConfig->getValue('settings/maintenance/maintenance_ip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($maintenance == '1') {
            /** @var \Magento\Framework\App\ObjectManager $om */
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Framework\Filesystem $filesystem */
            $filesystem = $om->get('Magento\Framework\Filesystem');
            /** @var \Magento\Framework\Filesystem\Directory\WriteInterface|\Magento\Framework\Filesystem\Directory\Write $writer */
            $writer = $filesystem->getDirectoryWrite('var');
            /** @var \Magento\Framework\Filesystem\File\WriteInterface|\Magento\Framework\Filesystem\File\Write $file */
            $file = $writer->openFile('.maintenance.flag', 'w');
            try {
                $file->lock();
                try {
                    $file->write('');
                } finally {
                    $file->unlock();
                }
            } finally {
                $file->close();
            }
        }
        /** @var \Magento\Framework\App\ObjectManager $om */
        $om_ip = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Filesystem $filesystem */
        $filesystem_ip = $om_ip->get('Magento\Framework\Filesystem');
        /** @var \Magento\Framework\Filesystem\Directory\WriteInterface|\Magento\Framework\Filesystem\Directory\Write $writer */
        $writer_ip = $filesystem_ip->getDirectoryWrite('var');
        /** @var \Magento\Framework\Filesystem\File\WriteInterface|\Magento\Framework\Filesystem\File\Write $file */
        $file_ip = $writer_ip->openFile('.maintenance.ip', 'w');
        try {
            $file_ip->lock();
            try {
                $file_ip->write($maintenance_ip);
            } finally {
                $file_ip->unlock();
            }
        } finally {
            $file_ip->close();
        }

        //generate css file for settings
        $psize = $this->scopeConfig->getValue('settings/general/paragraphsize', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $headingfont = $this->scopeConfig->getValue('settings/general/headingfont', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $pfont = $this->scopeConfig->getValue('settings/general/paragraphfont', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $bfont = $this->scopeConfig->getValue('settings/general/bodyfont', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $bline = $this->scopeConfig->getValue('settings/general/paragraphfontline', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $customCSS = $this->scopeConfig->getValue('settings/general/customcss', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $minicart = $this->scopeConfig->getValue('settings/minicart/type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $minicartRounded = $this->scopeConfig->getValue('settings/minicart/rounded_corners', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if($psize || $headingfont) {
            /** @var \Magento\Framework\App\ObjectManager $om */
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Framework\Filesystem $filesystem */
            $filesystem = $om->get('Magento\Framework\Filesystem');
            /** @var \Magento\Framework\Filesystem\Directory\WriteInterface|\Magento\Framework\Filesystem\Directory\Write $writer */
            $writer = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
            /** @var \Magento\Framework\Filesystem\File\WriteInterface|\Magento\Framework\Filesystem\File\Write $file */
            $file = $writer->openFile('vendor/crealevant/relevanttheme/web/css/relevant/settings.css', 'w');
            try {
                $file->lock();
                try {
                    if($headingfont == 'Playfair Display' || $pfont == 'Playfair Display') {
                        $file->write('@import url("https://fonts.googleapis.com/css?family=Playfair+Display:300,400,700");' . "\n");
                    }
                    if($headingfont == 'Roboto' || $pfont == 'Roboto') {
                        $file->write('@import url("https://fonts.googleapis.com/css?family=Roboto:300,400,500");' . "\n");
                    }
                    if($headingfont == 'Abril Fatface' || $pfont == 'Abril Fatface') {
                        $file->write('@import url("https://fonts.googleapis.com/css?family=Abril+Fatface");' . "\n");
                    }
                    if($headingfont == 'Pacifico' || $pfont == 'Pacifico') {
                        $file->write('@import url("https://fonts.googleapis.com/css?family=Pacifico");' . "\n");
                    }
                    if($headingfont == 'Open Sans Condensed' || $pfont == 'Open Sans Condensed') {
                        $file->write('@import url("https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700");' . "\n");
                    }
                    if($headingfont == 'Oswald' || $pfont == 'Oswald') {
                        $file->write('@import url("https://fonts.googleapis.com/css?family=Oswald:300,400");' . "\n");
                    }

                    $file->write('p { font-size: '.$psize.'px; font-family: '.$pfont.'; line-height: '.$bline.'%; }' . "\n");
                    $file->write('h1,h2,h3,h4 { font-family: "'.$headingfont.'", sans-serif!important; }' . "\n");
                    $file->write('body, a { font-family: "'.$bfont.'", sans-serif; }' . "\n");
                    if($minicart == '2') {
                        $file->write('.minicart-wrapper .active .block-minicart{opacity:1;transform:translate3d(-320px,0,0);-webkit-transform:translate3d(-320px,0,0);-moz-transform:translate3d(-320px,0,0);-o-transform:translate3d(-320px,0,0)}.minicart-wrapper .active:after{position:fixed;left:0;right:0;top:0;bottom:0;background-color:#000;opacity:.5;display:block;content:"";z-index:1999}.minicart-wrapper .block-minicart{background:#fff;top:0;bottom:0;width:320px;left:auto;right:-320px;position:fixed;z-index:2000;opacity:0;transition:all .5s;-webkit-transition:all .5s;-moz-transition:all .5s;-o-transition:all .5s}' . "\n");
                    }
                    if($minicartRounded == '1') {
                        $file->write('@media screen and (min-width: 992px) {
.minicart-wrapper .block-minicart {
    top: 38px;
    bottom: 38px;
    border-radius: 15px;
    right: -290px;
}
.cart .head.block-title {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}
.cart.from-right {
border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}
}
');
                    }
                    $file->write($customCSS);
                } finally {
                    $file->unlock();
                }
            } finally {
                $file->close();
            }
        }
    }
}
