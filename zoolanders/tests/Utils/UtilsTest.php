<?php

namespace ZFTests\Utils;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Utils\ArrayColumn;
use Zoolanders\Framework\Utils\IsString;
use Zoolanders\Framework\Utils\NameFromClass;

/**
 * Class UtilsTest
 * Utility helper classes tests
 *
 * @package ZFTests\Utils
 */
class UtilsTest extends ZFTestCase
{
    use IsString;

    /**
     * Test array column class methods
     *
     * @covers      ArrayColumn::array_column()
     */
    public function testArrayColumn(){
        //@TODO: Update when implement http request emulation under test env:
        $this->markTestSkipped('To be completed on Input assertions implemented');
    }

    /**
     * Check if provided bvalue is a string
     *
     * @covers          IsString::isString()
     * @dataProvider    dataSet
     */
    public function testIsString($value, $expected){
        // Check against isString method:
        $this->assertEquals($expected, $this->isString($value));
    }

    /**
     * IsString testing dataset
     */
    public function dataSet(){

        $obj = new \stdClass();

        return [
            [ 'alpha', true ],
            [ '42', true ],
            [ 42, false ],
            [ 0.56, false ],
            [ null, false ],
            [ [1, 2, 3], false ],
            [ $obj, false ]
        ];
    }
}
