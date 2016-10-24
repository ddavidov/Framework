<?php

namespace ZFTests\Dispatcher;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Dispatcher\Exception\ControllerNotFound;

/**
 * Class DispatcherTest
 * Test dispatching workflow
 */
class DispatcherTest extends ZFTestCase
{
    /**
     * Test dispatching front controller
     */
    public function testInvalidControllerException(){

        $dispatcher = $this->container->make('Zoolanders\Framework\Dispatcher\Dispatcher', array($this->container));
        $dispatcher->dispatch('Default');

        $this->expectException(ControllerNotFound::class);
        print "\n";
    }
}
