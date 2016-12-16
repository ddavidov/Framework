<?php

namespace ZFTests\Service;

use Zoolanders\Framework\Service\Params;

/**
 * Class ParamsServiceTest
 * Params Service tests
 *
 * @package ZFTests\Service
 */
class ParamsServiceTest extends ServiceTest
{
    /**
     * Test binding ops
     *
     * @covers
     */
    public function testBinding(){
        $params = new Params(self::$container);
    }
}
