<?php
define('DS', DIRECTORY_SEPARATOR);
define('_MODULE_DIR_', dirname(__FILE__));
define('_MODULE_APP_DIR_', _MODULE_DIR_ . DS . 'app');

require_once _MODULE_APP_DIR_ . DS . 'bootstrap.php';
ini_set('display_errors',1);
$bootstrap = new Bootstrap();
$bootstrap->init()->run();

