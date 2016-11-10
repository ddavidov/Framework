<?php

namespace ZFTests\Router;

use ZFTests\TestCases\ZFTestCase;

/**
 * Class RouterTest
 * Router testing class
 *
 * @package ZFTests\Router
 */
class RouterTest extends ZFTestCase
{
    /**
     * Test build route
     */
    public function testBuildRoute(){
        $this->markTestSkipped(
            'Build route is an abstraction'
        );
    }

    /**
     * Test parse route
     */
    public function testParseRoute(){
        $this->markTestSkipped(
            'Parse route is an abstraction'
        );
    }

    //@TODO: Cover with tests creating and clearing routing cache
}
