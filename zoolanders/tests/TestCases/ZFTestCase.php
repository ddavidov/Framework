<?php

namespace ZFTests\TestCases;

use PHPUnit\Framework\TestCase;
use Zoolanders\Framework\Container\Container;
use Joomla\Input\Input;
use Zoolanders\Framework\Service\Event;
use ZFTests\Classes\EventStackService;

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

        // Mocking service container:
        self::$container = new Container(array(
            'input' => new Input(),
            'joomla' => \JFactory::getApplication('site'),
            'zoo' => \App::getInstance('zoo'),
            'eventstack' => EventStackService::getInstance()
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

    /**
     * Assert event was triggered
     *
     * @param $eventName
     * @param callable $callback
     * @param string $message
     */
    public function assertEventTriggered($eventName, callable $callback, $message = ''){
        $eventStack = self::$container->eventstack;
        $offset = $eventStack->find($eventName);
        $this->assertThat(($offset !== false), new PHPUnit_Framework_Constraint_IsTrue, $message);

        if($offset !== false)
        {
            call_user_func($callback, $eventStack->get($offset));
        }
    }
}
