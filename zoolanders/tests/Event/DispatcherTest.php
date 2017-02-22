<?php

namespace ZFTests\Event;

use ZFTests\TestCases\ZFTestCase;
use ZFTests\Classes\TestEvent;
use Zoolanders\Framework\Event\Dispatcher;

/**
 * Class DispatcherTest
 * Event dispatcher class
 *
 * @package ZFTests\Event
 */
class DispatcherTest extends ZFTestCase
{
    public static $check = false;

    /**
     * Sample listening method
     */
    public static function listenerSample($event){
        self::$check = $event->getReturnValue();
    }

    /**
     * Connect disconnect has listeners test
     *
     * @covers  Dispatcher::connect()
     * @covers  Dispatcher::disconnect()
     * @covers  Dispatcher::hasListeners()
     * @covers  Dispatcher::getListeners()
     *
     * @dataProvider    listenersProvider
     */
    public function testConnectDisconnectListeners($listener){
        $dispatcher = new Dispatcher(self::$container->zoo);
        $this->assertFalse($dispatcher->hasListeners('test'));

        // Add listeners
        $dispatcher->connect('test', $listener);
        $this->assertTrue($dispatcher->hasListeners('test'));

        // Get listeners
        $listeners = $dispatcher->getListeners('test');
        $this->assertArraySubset([ $listener ], $listeners);

        // Remove listeners
        $dispatcher->disconnect('test', $listener);
        $this->assertFalse($dispatcher->hasListeners('test'));
    }

    /**
     * Test event notification
     *
     * @covers          Dispatcher::notify()
     * @depends         testConnectDisconnectListeners
     * @dataProvider    listenersProvider
     */
    public function testNotify($listener){
        $dispatcher = new Dispatcher(self::$container->zoo);
        $event = new TestEvent();
        $event->setReturnValue(true);

        // Add listener
        self::$check = false;
        $dispatcher->connect('classes:testevent', $listener);
        $dispatcher->notify($event);

        $this->assertTrue(self::$check);
    }

    /**
     * Test event triggering
     *
     * @covers      Dispatcher::trigger()
     * @depends     testNotify
     */
    public function testTriggerEvent(){
        $dispatcher = new Dispatcher(self::$container->zoo);
        $event = new TestEvent();

        $dispatcher->trigger($event);
        //$this->assertEventTriggered('classes:testevent', function($event){});
    }

    /**
     * Listeners data provider
     */
    public function listenersProvider(){
        return [
            [ self::class . '@listenerSample' ]
        ];
    }
}
