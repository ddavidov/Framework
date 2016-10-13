<?php

// Bootstraping Joomla env:
if(!defined('_JEXEC')){
    define('_JEXEC', 1);
}

// Bootstrap Framework
$loader = require_once(dirname(dirname(__FILE__)) . '/include.php');

$loader->addPsr4('ZFTests\\', dirname(__FILE__));
