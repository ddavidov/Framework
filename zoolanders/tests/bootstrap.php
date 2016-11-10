<?php

define('VENDOR_DIR', __DIR__."/../vendor/");

// Bootstrap Joomla env:

// Path to prepared joomla environment:
define('JOOMLA_ENV_PATH', VENDOR_DIR . 'ddavidov/joomla-zoo-env');

require_once JOOMLA_ENV_PATH . '/joomla-env-bootstrap.php';

// Bootstrap Framework Classes:
$loader = require_once(dirname(dirname(__FILE__)) . '/include.php');

$loader->addPsr4('ZFTests\\', dirname(__FILE__));
