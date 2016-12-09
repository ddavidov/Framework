<?php

namespace ZFTests\Service;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Service\Service;
use Zoolanders\Framework\Container\Container;

/**
 * Class ServiceTest
 * Service class test
 *
 * @package ZFTests\Service
 */
class ServiceTest extends ZFTestCase
{
    /**
     * Simple check of returning container instance
     *
     * @covers      Service::getContainer()
     */
    public function testGetContainer(){
        $service = new Service(self::$container);
        $this->assertTrue($service->getContainer() instanceof Container);
    }
}
