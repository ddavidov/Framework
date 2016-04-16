<?php

namespace Zoolanders\Container;

use Pimple\Container as Pimple;
use Zoolanders\Filesystem\Filesystem;
use Zoolanders\Zoo\Zoo;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

class Container extends Pimple
{
    /**
     * The container instance
     * @var Container
     */
    protected static $container = null;

    /**
     * The path to the config file
     * @var string
     */
    protected static $configFile = 'libraries/zoolanders/Framework/config.json';

    /**
     * Load the service into the DI Container
     * @param $services
     */
    protected function loadServices($services)
    {
        // Load the services
        foreach ($services as $name => $class) {
            // it's either an array or an object,
            if (is_object($class) || is_array($class) || $class instanceof Registry){
                $tmp = new Container();
                $tmp->loadServices($class);
                $this[$name] = $tmp;
                continue;
            }

            // Otherwise add the service
            if (!isset($this[$name])) {
                $this[$name] = function (Container $c) use ($class) {
                    return new $class($c);
                };
            }
        }
    }

    /**
     * Singleton pattern
     * @return Container
     */
    public static function &getInstance($values = [])
    {
        if (self::$container) {
            return self::$container;
        }

        $container = new Container($values);

        // get the config file
        $config = new Registry();
        $config->loadFile(JPATH_SITE . '/' . self::$configFile);

        // load the services classes from the config file
        $services = $config->get('services', []);
        $container->loadServices($services);

        // Database Driver service
        if (!isset($container['db'])) {
            $container['db'] = function () use ($container) {
                return $container['zoo']->database;
            };
        }

        self::$container = $container;

        return self::$container;
    }

    /**
     * Magic getter for alternative syntax, e.g. $container->foo instead of $container['foo']
     *
     * @param   string $name
     *
     * @return  mixed
     *
     * @throws \InvalidArgumentException if the identifier is not defined
     */
    function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Magic setter for alternative syntax, e.g. $container->foo instead of $container['foo']
     *
     * @param   string $name The unique identifier for the parameter or object
     * @param   mixed $value The value of the parameter or a closure for a service
     *
     * @throws \RuntimeException Prevent override of a frozen service
     */
    function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }
}