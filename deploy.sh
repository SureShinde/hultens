php bin/magento maintenance:enable
composer install
php bin/magento setup:upgrade
php -dmemory_limit=5G bin/magento setup:di:compile
php -dmemory_limit=5G bin/magento setup:static:deploy en_US
php -dmemory_limit=5G bin/magento setup:static:deploy sv_SE
php -dmemory_limit=5G bin/magento setup:static:deploy da_DK
php bin/magento cache:flush
php bin/magento cache:clean
php bin/magento maintenance:disable
