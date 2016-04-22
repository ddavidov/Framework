<?php

namespace Zoolanders\Container;

use Pimple\Container as Pimple;
use Zoolanders\Event\ContainerConfigurationLoaded;
use Zoolanders\Event\ContainerServicesLoaded;
use Zoolanders\Filesystem\Filesystem;
use Zoolanders\Service\Event;
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
     * @var Registry
     */
    protected $config;

    /**
     * Load the service into the DI Container
     * @param $services
     */
    protected function loadServices($services)
    {
        // Load the services
        foreach ($services as $name => $class) {
            // it's either an array or an object,
            if (is_object($class) || is_array($class) || $class instanceof Registry) {
                $tmp = new Nested();
                $tmp->setParentContainer($this);
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

        // Database Driver service
        if (!isset($container['zoo'])) {
            $container['zoo'] = function () use ($container) {
                return new \Zoolanders\Service\Zoo($container);
            };
        }

        // Database Driver service
        if (!isset($container['db'])) {
            $container['db'] = function () use ($container) {
                return $container['zoo']->database;
            };
        }

        // Event service
        if (!isset($container['event'])) {
            $container['event'] = function () use ($container) {
                return new \Zoolanders\Service\Event($container);
            };
        }

        // get the config file
        $config = new Registry();
        $config->loadFile(JPATH_SITE . '/' . self::$configFile);

        // trigger an event to make the configuration extendable
        // it's a joomla event since we've yet to return the container on which we
        // could register a zoolanders core event
        $container->event->joomla->trigger('onContainerConfigurationLoaded', array(&$config));

        // Load the configuration file
        $container->loadConfig($config);

        self::$container = $container;

        return self::$container;
    }

    /**
     * Load the configuration
     * @param Registry $config
     */
    public function loadConfig(Registry $config)
    {
        // Merge it with the current config
        if ($this->config) {
            $this->config->merge($config, true);
        } else {
            $this->config = $config;
        }

        // load the services classes from the config file
        $services = $config->get('services', []);
        $this->loadServices($services);

        // Notify we've loaded the services
        $this->event->joomla->trigger('onContainerServicesLoaded', array($services));

        // Bind any listener for events
        $services = $config->get('events', []);
        $this->bindEvents($services);
    }

    /**
     * @param $events
     */
    protected function bindEvents($events)
    {
        foreach ($events as $event => $listeners) {
            $listeners = (array) $listeners;

            foreach ($listeners as $listener) {
                $parts = explode("@", $listener);

                $method = $listener;

                // we have a function to call
                if (count($parts) == 2) {
                    $method = $parts;
                }

                if (class_exists($listener)) {
                    $method = [new $listener, 'handle'];
                }

                $this->event->dispatcher->connect($event, $method);
            }
        }
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