<?php

namespace ZFTests\Model;

use ZFTests\TestCases\ZFTestCaseFixtures;

/**
 * Class ItemTest
 * Item test
 *
 * @package ZFTests\Model
 */
class ItemTest extends ZFTestCaseFixtures
{
    /**
     * Test creating instance
     */
    public function testMake(){

        $this->assertTableHasRow('zoo_item', [
           'alias' => 'test-item'
        ]);
    }
}
