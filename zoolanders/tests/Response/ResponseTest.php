<?php

namespace ZFTests\Response;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Response\Response;

/**
 * Class ResponseTest
 * Http response wrapper
 *
 * @package ZFTests\Response
 */
class ResponseTest extends ZFTestCase
{
    /**
     * Response create test
     *
     * @covers          Response::create()
     * @dataProvider    statusCodesProvider
     */
    public function testResponseCreating($pass, $expect){
        if($pass === null){
            $response = new Response();
        } else {
            $response = new Response($pass);
        }

        $this->assertEquals($expect, $response->code);
    }

    /**
     * Test get set ops
     *
     * @covers          Response::get()
     * @covers          Response::set()
     *
     * @dataProvider    bindingDataSet
     */
    public function testSetGet($key, $value){
        $response = new Response();

        $this->assertEmpty($response->{$key});

        $response->set($key, $value);
        $this->assertEquals($value, $response->{$key});
    }

    /**
     * Test set get header ops
     *
     * @covers          Response::setHeader()
     */
    public function testSetGetHeader(){
        // @TODO: Add logics
    }

    /**
     * Status codes data provider
     */
    public function statusCodesProvider(){
        return  [
            [ null, 200 ],
            [ 200, 200 ],
            [ 401, 401 ],
            [ 403, 403 ],
            [ 404, 404 ],
            [ 500, 500 ]
        ];
    }

    /**
     * Binding data set
     */
    public function bindingDataSet(){
        return [
            ['alpha', 'A'],
            ['bravo', 'b'],
            ['charlie', 1],
            ['delta', false],
            ['echo', array()]
        ];
    }
}
