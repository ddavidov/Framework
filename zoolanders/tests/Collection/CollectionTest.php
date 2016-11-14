<?php

namespace ZFTests\Collection;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Collection\Collection;

/**
 * Class CollectionTest
 * Collection class tests
 *
 * @package ZFTests\Collection
 */
class CollectionTest extends ZFTestCase
{
    /**
     * Test creating collection from an array
     *
     * @dataProvider    dataProvider
     * @covers          Collection::make()
     * @covers          Collection::all()
     */
    public function testMakeCollection($items){
        $collection = Collection::make($items);

        //Check if all the items are present in the resulting collection:
        $this->assertArraySubset($items, $collection->all());
    }

    /**
     * Test collapsing the collection
     *
     * @depends         testMakeCollection
     * @covers          Collection::collapse()
     */
    public function testCollapse(){
        $src_array = [[0,1], [2,3], [4,5]];
        $collection = Collection::make($src_array);

        // Check if items array is transformed correct:
        $this->assertArraySubset( [0, 1, 2, 3, 4, 5], $collection->collapse()->all());
    }

    /**
     * Data provider for collection ops testing
     */
    public function dataProvider(){

        return [
            [
                [1, 2, 3]
            ]
        ];
    }

    /**
     * Array data provider for collection ops testing
     */
    public function arrayDataProvider(){

        return [
            [
                [[0, 1], [2, 3], [4, 5]]
            ]
        ];
    }
}
