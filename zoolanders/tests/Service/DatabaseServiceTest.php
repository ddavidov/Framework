<?php

namespace ZFTests\Service;

use Zoolanders\Framework\Service\Database;
use ZFTests\TestCases\ZFTestCaseFixtures;
use ZFTests\Classes\CSVData;
use ZFTests\Classes\Tag;

/**
 * Class DatabaseServiceTest
 * Database service test class
 *
 * @package ZFTests\Service
 */
class DatabaseServiceTest extends ZFTestCaseFixtures
{
    use CSVData;

    /**
     * @var string  Test cases source relative path
     */
    protected $_data_source = '/querying/database_service.csv';

    /**
     * Test db init
     */
    public function testDBInit(){
        $db = new Database(self::$container);

        $this->assertTrue(in_array($db->name, ['mysql', 'mysqli', 'postgresql', 'sqlite']));
    }

    /**
     * Test DB querying
     *
     * @covers      Database::query()
     */
    public function testDBQuery(){
        $db = new Database(self::$container);

        $sql = "INSERT INTO `#__zoo_tag` (`item_id`, `name`) VALUES(1, 'test')";
        $result = $db->query($sql);

        $this->assertNotFalse($result);
        $this->assertTableHasRow('zoo_tag', ['item_id'=>1, 'name'=>'test']);

        $sql = "DELETE FROM `#__zoo_tag` WHERE item_id=1 AND name='test'";
        $result = $db->query($sql);

        $this->assertNotFalse($result);
        $this->assertTableHasNoRow('zoo_tag', ['item_id'=>1, 'name'=>'test']);
    }

    /**
     * Run to check query against src data and expected result
     *
     * @covers          Database::queryResult()
     */
    public function testQuerying(){

        $data = $this->loadTestDataCSV(FIXTURES_PATH . $this->_data_source);
        if(!empty($data)){
            foreach($data as $case){
                $methodName = array_shift($case);
                if(!empty($methodName)){
                    $methodName = 'query' . ucfirst($methodName);
                    $args = @array_shift($case);
                    $expected = eval('return ' . @array_shift($case) . ';');

                    $db = new Database(self::$container);

                     $reflection = new \ReflectionClass($db);
                    if($reflection->hasMethod($methodName)){
                        $methodReflection = $reflection->getMethod($methodName);
                        $result = $methodReflection->invokeArgs($db, (array)$args);

                        $this->assertEquals($expected, $result, sprintf('Calling %s with provided params assertion failed', $methodName));
                    }
                }
            }
        }
    }

    /**
     * Test table pfx replace
     *
     * @covers          Database::replacePrefix()
     */
    public function testPrefixReplace(){
        $db = new Database(self::$container);
        $expected = $db->getPrefix() . 'zoo_item';

        $this->assertEquals($expected, $db->replacePrefix('#__zoo_item'));
    }

    /**
     * Test insert object method
     *
     * @covers          Database::insertObject()
     */
    public function testInsertObject(){

        if(version_compare(PHP_VERSION, '7.0.0', '>='))
        {
            $this->markTestSkipped('Skipped due to PHP7 compatibility issue.');
        }

        $db = new Database(self::$container);

        $tag = new Tag();
        $tag->item_id = 1;
        $tag->name = 'test';

        $result = $db->insertObject('#__zoo_tag', $tag);

        // Check if record appeared in DB:
        $this->assertNotFalse($result);
        $this->assertTableHasRow('zoo_tag', ['item_id' => $tag->item_id, 'name' => $tag->name]);

        // Cleanup the insertion:
        $sql = "DELETE FROM `#__zoo_tag` WHERE item_id=1 AND name='test'";
        $db->query($sql);
    }
}
