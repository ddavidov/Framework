<?php

namespace ZFTests\TestCases;

use ZFTests\Classes\FixtureImporter;

/**
 * Class ZFTestCaseFixtures
 * @package ZFTests\TestCases
 */
class ZFTestCaseFixtures extends ZFTestCase
{
    /**
     * SetUp before class
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$container['fixtures'] = new FixtureImporter(self::$container, [
            'path' => 'sql'
        ]);

        self::raiseFixtures();
    }

    /**
     * Tear down after class
     */
    public static function tearDownAfterClass()
    {
        self::dropFixtures();

        parent::tearDownAfterClass();
    }

    /**
     * Test fixture workflow
     */
    protected static function raiseFixtures(){
        $dbo = self::$container->db;
        $dbo->transactionStart();

        self::$container->fixtures->import(self::getPkgName());
    }

    /**
     * Test fixtures remove
     */
    protected static function dropFixtures(){
        $dbo = self::$container->db;
        $dbo->transactionRollback();
    }

    /**
     * Define fixture pkg name
     */
    protected static function getPkgName(){
        $parts = explode('\\', static::class);
        return strtolower(preg_replace('/Test$/Ui', '', array_pop($parts)));
    }
}
