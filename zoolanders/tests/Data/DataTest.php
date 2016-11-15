<?php

namespace ZFTests\Data;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Data\Data;

/**
 * Class DataTest
 * Data struct unit test
 *
 * @package ZFTests\Data
 */
class DataTest extends ZFTestCase
{
    /**
     * Data asseting test
     *
     * @covers  Data::create()
     * @covers  Data::get()
     * @covers  Data::set()
     * @covers  Data::has()
     * @dataProvider arrayDataSet
     */
    public function testGetSetHas($dataset, $control_value){
        $obj = new Data($dataset);
        $this->assertTrue($obj->has($control_value));

        // Test getters / setters:
        $obj->set('f', 'foxtrot');
        $this->assertTrue($obj->has('f'));
        $this->assertEquals('foxtrot', $obj->get('f'));

        // Test "magic" getter/setter:
        $obj->e = 'echo';
        $this->assertTrue($obj->has('e'));
        $this->assertEquals('echo', $obj->get('e'));
    }

    /**
     * Test removing
     *
     * @depends         testGetSetHas
     * @covers          Data::remove()
     * @dataProvider    arrayDataSet
     */
    public function testRemove($dataset, $control_value){
        $obj = new Data($dataset);
        $this->assertTrue($obj->has($control_value));

        // Removing element from the dataset:
        $obj->remove($control_value);
        $this->assertFalse($obj->has($control_value));
    }

    /**
     * Test search function
     *
     * @covers          Data::find()
     * @dataProvider    lookupDataSet
     */
    public function testFind($dataset, $needle, $expected){
        $obj = new Data($dataset);
        // Check if we found expected value:
        $this->assertEquals($expected, $obj->find($needle, 'zulu'));
    }

    /**
     * Test search recursive method
     *
     * @covers          Data::searchRecursive()
     * @dataProvider    lookupDataSet
     */
    public function testSearchRecursive($dataset, $needle, $expected){
        $obj = new Data($dataset);

        $this->markTestSkipped('searchRecursive method requires fixes');
    }

    /**
     * Test flattern array
     *
     * @covers          Data::flattenRecursive()
     * @dataProvider    flatternDataSet
     */
    public function testFlattern($dataset, $expected){
        $obj = new Data($dataset);

        // Check if transformation was correct:
        $this->assertEquals($expected, $obj->flattenRecursive());
    }

    /**
     * Array Data provider
     */
    public function arrayDataSet(){
        return [
            [
                [1, 2, 3],
                2
            ],
            [
                ['a' => 'alpha', 'b' => 'bravo', 'c' => 'charlie'],
                'b'
            ]
        ];
    }

    /**
     * Data provider for search/find ops testing:
     */
    public function lookupDataSet(){
        return [
            [
                [1,2,3],
                2,
                3
            ],
            [
                [
                    'a' => 'alpha',
                    'b' => 'bravo',
                    'c' => 'charlie'
                ],
                'b',
                'bravo'
            ],
            [
                ['squad' =>
                    [
                        'corporal' => 'John',
                        'sergeant' => 'Peter',
                        'captain' => 'Jack'
                    ]
                ],
                'squad.captain',
                'Jack'
            ],
            [
                [1,2,3],
                3,
                'zulu'
            ]
        ];
    }

    /**
     * Flattern methods testing data set
     */
    public function flatternDataSet(){
        return [
            [
                [
                    [1, 2, 3], 4, 5
                ],
                [
                    1, 2, 3, 4, 5
                ]
            ],
            [
                ['a' => 'alpha', 'b' => 'bravo'],
                ['alpha', 'bravo']
            ]
        ];
    }
}
