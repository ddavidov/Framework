<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Container;

use Auryn\Injector;
use Pimple\Container as Pimple;
use Zoolanders\Framework\Autoloader;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * Class Container
 * @package Zoolanders\Framework\Container
 *
 * @property-read   \Zoolanders\Framework\Service\Inflector $inflector
 * @property-read   \Zoolanders\Framework\Service\Filesystem $filesystem
 * @property-read   \Zoolanders\Framework\Service\Path $path
 * @property-read   \Zoolanders\Framework\Container\Nested\System $system
 * @property-read   \Zoolanders\Framework\Container\Nested\Assets $assets
 * @property-read   \Zoolanders\Framework\Service\Factory $factory
 * @property-read   \Zoolanders\Framework\Service\Zoo $zoo
 * @property-read   \Zoolanders\Framework\Service\Database $db
 * @property-read   \Zoolanders\Framework\Service\Database $database
 * @property-read   \Zoolanders\Framework\Service\Event $event
 * @property-read   \Zoolanders\Framework\Service\Date $date
 * @property-read   \Zoolanders\Framework\Service\Request $input
 * @property-read   \Zoolanders\Framework\Service\Request $request
 * @property-read   \Zoolanders\Framework\Service\Params $params
 * @property-read   \Zoolanders\Framework\Service\Joomla $joomla
 * @property-read   \Zoolanders\Framework\Service\System\Document $document
 * @property-read   \Zoolanders\Framework\Service\Environment $environment
 * @property-read   \Zoolanders\Framework\Service\Installation $installation
 * @property-read   \Zoolanders\Framework\Service\Dependencies $dependencies
 * @property-read   \Zoolanders\Framework\Service\Route $route
 * @property-read   \Zoolanders\Framework\Service\Crypt $crypt
 * @property-read   \Zoolanders\Framework\Service\Data $data
 * @property-read   \Zoolanders\Framework\Service\Link $link
 * @property-read   \Zoolanders\Framework\Service\Cache $cache
 * @property-read   \Zoolanders\Framework\Service\User $user
 * @property-read   \Zoolanders\Framework\Service\Zip $zip
 */
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
     * @var Injector
     */
    protected $injector;

    /**
     * @var Autoloader
     */
    public $loader;

    /**
     * Container constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        parent::__construct($values);

        $this->injector = new Injector();

        // Forcibly share the container
        $this->injector->share($this);

        $this->loader = Autoloader::getInstance();
    }

    /**
     * Proxy any other call to the injector
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->injector, $name], $arguments);
    }

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
                $tmp = new Nested([]);
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
                return new \Zoolanders\Framework\Service\Zoo($container);
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
                return new \Zoolanders\Framework\Service\Event($container);
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
        if (!empty($this->event)) {
            $this->event->joomla->trigger('onContainerServicesLoaded', array($services));

            // Bind any listener for events
            $services = $config->get('events', []);
            $this->bindEvents($services);
        }
    }

    /**
     * @param $events
     */
    protected function bindEvents($events)
    {
        $this->event->dispatcher->bindEvents($events);
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
