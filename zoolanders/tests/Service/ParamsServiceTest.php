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
     * @covers          Params::get()
     * @covers          Params::set()
     * @covers          Params::save()
     * @covers          Params::reload()
     *
     * @dataProvider    bindingDataSet
     */
    public function testBinding($key, $value){
        $params = new Params(self::$container);
        $db = self::$container->db;
        $db->transactionStart();

        // Check get/set ops:
        $params->set($key, $value);
        $this->assertEquals($value, $params->get($key));
        // Check reload:
        $params->reload();
        $this->assertFalse($params->get($key, false));
        // Reset, save and check:
        $params->set($key, $value);
        $params->save();
        $params->reload();
        // Now value should persist
        $this->assertEquals($value, $params->get($key));
        // Drop params:
        $db->transactionRollback();
    }

    /**
     * Test mass assign functionality
     *
     * @depends         testBinding
     * @covers          Params::setParams()
     * @covers          Params::getParams()
     *
     * @dataProvider    bindingDataSet
     */
    public function testMassBinding($key, $value){
        $params = new Params(self::$container);
        $db = self::$container->db;
        $db->transactionStart();

        // Check get/set ops:
        $params->setParams([$key => $value]);
        $this->assertArraySubset([ 'com_zoo' => [$key => $value] ], $params->getParams());
        $params->save();
        $params->reload();
        $this->assertArraySubset([ 'com_zoo' => [$key => $value] ], $params->getParams());
        // Drop params:
        $db->transactionRollback();
    }

    /**
     * Binding test data set
     */
    public function bindingDataSet(){
        return [
            [ 'a', 'alpha' ],
            [ 'b', 'bravo' ],
            [ 'c', ['charlie'] ]
        ];
    }
}
