<?php

namespace ZFTests\TestCases;

use PHPUnit\Framework\TestCase;
use Zoolanders\Framework\Container\Container;

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

        self::$container = new Container(array());
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
