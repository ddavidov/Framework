<?php

namespace ZFTests\TestCases;

use PHPUnit\Framework\TestCase;
use Zoolanders\Framework\Container\Container;
use Joomla\Input\Input;
use \Zoolanders\Framework\Service\Event;

/**
 * Class ZFTestCase
 * Extended TestCase class
 *
 * @package Zoolanders\Framework\TestCases
 */
class ZFTestCase extends TestCase
{
    /**
     * @var DI container
     */
    protected static $container;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $config = new \JConfig();

        // Mocking service container:
        self::$container = new Container(array(
            'input' => new Input(),
            'joomla' => \JFactory::getApplication('site', get_object_vars($config)),
            'zoo' => null
        ));
        self::$container['event'] = new Event(self::$container);
    }

    public static function tearDownAfterClass()
    {
        self::$container = null;

        parent::tearDownAfterClass();
    }

    /**
     * Magic getter for container
     *
     * @param   string
     *
     * @return  mixed
     */
    public function __get($name){
        if ($name == 'container') {
            return self::$container;
        } else {
            return parent::__get($name);
        }
    }
}
