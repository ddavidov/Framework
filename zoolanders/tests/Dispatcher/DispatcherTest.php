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
    public function testEnv(){

        // Test if tests were launched
        $this->assertTrue(true);
    }

    /**
     * Test dispatching front controller
     */
    public function testInvalidControllerException(){

        $this->expectException(ControllerNotFound::class);

        $dispatcher = $this->container->make('Zoolanders\Framework\Dispatcher\Dispatcher', array($this->container));
        $dispatcher->dispatch('Default');
    }
}
