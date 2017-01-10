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
     * @var string  Test cases source relative path
     */
    protected $_data_source = '/querying/database.csv';

    /**
     * Creates and returns instance of testing class
     */
    protected function getTestInstance(){

        return new DatabaseModel(self::$container);
    }

    /**
     * Load and parse CSV file with test cases
     *
     * @param $filePath
     * @return mixed
     * @throws \Exception
     */
    protected function loadTestDataCSV($filePath){
        if(file_exists($filePath)){
            $data = [];
            $content = file_get_contents($filePath);
            $strings = explode("\n", $content);
            if(!empty($strings)){
                foreach ($strings as $str){
                    $data[] = str_getcsv ( $str );
                }
            }
            return $data;
        } else {
            throw new \Exception('CSV file ['.$filePath.'] with test data not found');
        }
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
     * Test query building methods, using test cases from data file
     */
    public function testQuerying(){
        $data = $this->loadTestDataCSV(FIXTURES_PATH . $this->_data_source);
        if(!empty($data)){
            foreach($data as $case){
                $methodName = array_shift($case);
                if(!empty($methodName)){
                    $args = @array_shift($case);
                    $expected = @array_shift($case);

                    if($args){
                        $args = eval(sprintf('return %s;', $args));
                    }

                    $dbm = $this->getTestInstance();

                    // Bind first where clause for alternatives
                    if(preg_match('/^or/iU', $methodName)){
                        $dbm->orWhere('id','=','1');
                    }

                    $reflection = new \ReflectionClass($dbm);
                    if($reflection->hasMethod($methodName)){
                        $methodReflection = $reflection->getMethod($methodName);
                        $methodReflection->invokeArgs($dbm, $args);
                        $dbm->buildQuery();

                        $this->assertEquals($expected, str_replace("\n", '', $dbm->getQuery()->__toString()));
                    }
                }
            }
        }
    }

    /**
     * Get set table prefix methods test
     *
     * @covers          Database::setTablePrefix()
     * @covers          Database::getTablePrefix()
     *
     * @dataProvider    prefixDataProvider
     */
    public function testGetSetPrefix($prefix, $expected){
        $dbm = $this->getTestInstance();

        // Get set operations:
        $dbm->setTablePrefix($prefix);
        $this->assertEquals($prefix, $dbm->getTablePrefix($prefix));

        $dbm->where('id','=','1');
        $dbm->buildQuery();

        // Check if affects queries
        //echo $dbm->getQuery()->__toString();
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
     * Test prefix data provider
     */
    public function prefixDataProvider(){
        return [
            ['a', ''],
            ['b', ''],
            ['c', '']
        ];
    }

}
