<?php

namespace ZFTests\TestCases;


class ZFTestCaseFixtures extends ZFTestCase
{
    /**
     * SetUp before class
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

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
        $dbo = \JFactory::getDbo();
        $dbo->transactionStart();

        //@TODO: Import fixture data from sql file
    }

    /**
     * Test fixtures remove
     */
    protected static function dropFixtures(){
        $dbo = \JFactory::getDbo();
        $dbo->transactionRollback();
    }
}
