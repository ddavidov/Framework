<?php

namespace Zoolanders\Service;
use Zoolanders\Service\Service;

/**
 * Class System
 * @package Zoolanders\System
 */
abstract class System extends Service
{
    /**
     * Proxy function calls to the J* object named like this class
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $method = 'get' . ucfirst(strtolower($this->getName()));

        // this is JFactory::get****()->$method($args)
        return call_user_func_array([call_user_func('\JFactory::' . $method), $name], $arguments);
    }

    /**
     * Get the name of the current service
     * @return string
     */
    public function getName()
    {
        $parts = explode("\\", get_class($this));
        $name = array_pop($parts);

        return $name;
    }
}