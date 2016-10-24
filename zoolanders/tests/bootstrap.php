<?php

// Bootstrap Joomla env:
require("config/configuration.php");
require("../vendor/joomla/joomla-platform/libraries/import.legacy.php");
require("../vendor/joomla/joomla-platform/libraries/import.php");

if(!defined('_JEXEC')){
    define('_JEXEC', 1);
}

// Bootstrap Framework
$loader = require_once(dirname(dirname(__FILE__)) . '/include.php');

$loader->addPsr4('ZFTests\\', dirname(__FILE__));
