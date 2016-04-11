<?php

namespace Zoolanders;

use Composer\Autoload\ClassLoader;

class Autoloader
{
    /**
     * @var ClassLoader
     */
    private static $loader;

    /**
     * Autoloader constructor. Private!!! Just use getInstance
     * @param ClassLoader $loader
     */
    private function __construct(ClassLoader $loader = false)
    {
        if ($loader) {
            self::$loader = $loader;
        }
    }

    /**
     * Proxy calls to the loader of Composer
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(self::$loader, $arguments);
    }

    /**
     * Singleton pattern
     * @param ClassLoader $loader
     * @return ClassLoader|Autoloader
     */
    public static function &getInstance(ClassLoader $loader = false)
    {
        if (self::$loader !== null) {
            return self::$loader;
        }

        self::$loader = new Autoloader($loader);

        return self::$loader;
    }
}

$loader = require_once './vendor/autoload.php';

// Create autoloader and add the mapping to the framework
$loader = Autoloader::getInstance($loader);
$loader->addPsr4('Zoolanders\\', dirname(__FILE__) . '/Framework');

return $loader;