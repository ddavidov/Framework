<?php

define('VENDOR_DIR', __DIR__."/../vendor/");

// Bootstrap Joomla env:
require("config/configuration.php");
require(VENDOR_DIR . "joomla/joomla-platform/libraries/import.legacy.php");
require(VENDOR_DIR . "joomla/joomla-platform/libraries/import.php");

if(!defined('_JEXEC')){
    define('_JEXEC', 1);
}

if(!defined('JPATH_SITE')){
    define('JPATH_SITE', __DIR__);
}

// Bootstrap ZOO:
require(VENDOR_DIR . "ddavidov/xoo/config.php");

// Bootstrap Framework
$loader = require_once(dirname(dirname(__FILE__)) . '/include.php');

$loader->addPsr4('ZFTests\\', dirname(__FILE__));
