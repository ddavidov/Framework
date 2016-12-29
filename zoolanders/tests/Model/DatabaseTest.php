<?php

namespace ZFTests\Model;

use ZFTests\TestCases\ZFTestCaseFixtures;
use ZFTests\Classes\DatabaseModel;
use Zoolanders\Framework\Model\Database;

/**
 * Class DatabaseTest
 * Database abstract class
 *
 * @package ZFTests\Model
 */
class DatabaseTest extends ZFTestCaseFixtures
{
    /**
     * Creates and returns instance of testing class
     */
    protected function getTestInstance(){

        return new DatabaseModel(self::$container);
    }

    /**
     * Test fields specifying for query
     *
     * @covers          Database::fields()
     * @dataProvider    fieldsetProvider
     */
    public function testFields($fieldset, $expected){
        $dbm = $this->getTestInstance();
        $dbm->fields($fieldset);

        $this->assertEquals($expected, str_replace("\n",'',$dbm->getQuery()->__toString()));
    }

    /**
     * Test where clause building with different cases
     *
     * @covers          Database::where()
     * @dataProvider    whereClauseProvider
     */
    public function testWhere($params, $expected){
        $dbm = $this->getTestInstance();
        extract($params);
        $dbm->where($name, $operator, $value);
        $dbm->buildQuery();

        $this->assertEquals($expected, str_replace("\n",'',$dbm->getQuery()->__toString()));
    }

    /**
     * Fieldset provider
     */
    public function fieldsetProvider(){
        return [
            [ ['id'], "SELECT *,`id`FROM ``" ],
            [ ['id','alias'], "SELECT *,`id`,`alias`FROM ``" ]
        ];
    }

    /**
     * Where clause testing dataset
     */
    public function whereClauseProvider()
    {
        return [
            [ ['name' => 'id', 'operator' => '=', 'value' => 1], 'SELECT *FROM ``WHERE `id` = \'1\'']
        ];
    }
}
