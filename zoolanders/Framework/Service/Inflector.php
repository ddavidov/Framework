<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Container\Container;

class Inflector extends Service
{
    /**
     * @var \Joomla\String\Inflector
     */
    protected $inflector;

    /**
     * Constructor
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        parent::__construct($c);

        $this->inflector = \Joomla\String\Inflector::getInstance();
    }

    /**
     * @param $word
     * @return mixed
     */
    public function camelize($word)
    {
        $word = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $word);
        $word = str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $word))));

        return $word;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->inflector, $name], $arguments);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->inflector->$name;
    }
}