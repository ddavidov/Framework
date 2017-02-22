<?php

namespace ZFTests\Dispatcher;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Dispatcher\Exception\ControllerNotFound;
use Zoolanders\Framework\Request\Request;

/**
 * Class DispatcherTest
 * Test dispatching workflow
 */
class DispatcherTest extends ZFTestCase
{
    /**
     * Test dispatching front controller
     *
     * @covers      Dispatcher::dispatch()
     */
    public function testInvalidControllerException(){

        $this->expectException(ControllerNotFound::class);

        $dispatcher = $this->container->make('Zoolanders\Framework\Dispatcher\Dispatcher', array($this->container));
        $dispatcher->dispatch(new Request());

        // Check if expected events were triggered
        $this->assertEventTriggered('dispatcher:beforedispatch', function(){});
        $this->assertEventTriggered('dispatcher:afterdispatch', function(){});
    }
}
