<?php

namespace Zoolanders\Container;

use Pimple\Container as Pimple;
use Zoolanders\Filesystem\Filesystem;
use Zoolanders\Zoo;

defined('_JEXEC') or die;

class Container extends Pimple
{
    /**
     * The container instance
     * @var Container
     */
    protected static $container = null;

    /**
     * Container constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        // Zoo service to proxy stuff to zoo's framework
        if (!isset($this['zoo'])) {
            $this['zoo'] = function (Container $c) {
                return new Zoo($c);
            };
        }

        // Filesystem abstraction service
        if (!isset($this['filesystem'])) {
            $this['filesystem'] = function (Container $c) {
                return new Filesystem($c);
            };
        }

        // Database Driver service
        if (!isset($this['db'])) {
            $this['db'] = function () {
                return $this['zoo']->database;
            };
        }
    }

    /**
     * Singleton pattern
     * @return Container
     */
    public static function &getInstance(array $values = [])
    {
        if (self::$container) {
            return self::$container;
        }

        return new Container($values);
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