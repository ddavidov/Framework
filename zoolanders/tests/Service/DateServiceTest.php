<?php

namespace ZFTests\Service;

use Zoolanders\Framework\Service\Date;

/**
 * Class DateServiceTest
 * Date operations service tests
 *
 * @package ZFTests\Service
 */
class DateServiceTest extends ServiceTest
{
    const HOUR = 3600;
    const DAY = 24 * self::HOUR;
    const WEEK = self::DAY * 7;
    const MONTH = self::WEEK * 4;

    /**
     * Test date create operation
     *
     * @covers          Date::create()
     * @covers          Date::isToday()
     * @covers          Date::isYesterday()
     */
    public function testCreate(){
        $ds = new Date(self::$container);
        $date = $ds->create();

        // Check current date:
        $this->assertInstanceOf('\JDate', $date);
        $this->assertTrue($ds->isToday($date));

        // Check different date:
        $past = $date = $ds->create(time() - (3600*24+10)); // 1 day ago
        $this->assertFalse($ds->isToday($past));
        $this->asserttrue($ds->isYesterday($past));
    }

    /**
     * Test delta or weekday text func.
     *
     * @covers          Date::getDeltaOrWeekdayText()
     *
     * @dataProvider    deltaTextDataSet
     */
    public function testDelta($src, $expected){
        $ds = new Date(self::$container);
        $date = $ds->create($src);

        if((int)date('H')>2)
        {
            $this->assertEquals($expected, $ds->getDeltaOrWeekdayText($date));
        } else {
            $this->markTestSkipped('Unable to test this func properly with current data set');
        }
    }

    /**
     * Test date format transformations
     *
     * @covers          Date::format()
     * @covers          Date::strftimeToDateFormat()
     *
     * @dataProvider    formatDataSet
     */
    public function testFormat($original, $expected){
        $ds = new Date(self::$container);

        $this->assertEquals($expected, $ds->format($original));
        $this->assertEquals($expected, $ds->strftimeToDateFormat($original));
    }

    /**
     * Test reversion date format transformations
     *
     * @covers          Date::dateFormatToStrftime()
     *
     * @dataProvider    bkFormatDataSet
     */
    public function testDateFormat($original, $expected){
        $ds = new Date(self::$container);

        $this->assertEquals($expected, $ds->strftimeToDateFormat($original));
    }

    /**
     * Test date preset functions e.g. dateOnly, getDateTime, etc.
     *
     * @covers          Date::getDateOnly()
     * @covers          Date::getDateTime()
     */
    public function testDateCropFunctions(){
        $ds = new Date(self::$container);
        $date = $ds->create();
        // @TODO: Need user for this
        $this->markTestSkipped('User required');
    }

    /**
     * Dataset to test delta text date function
     */
    public function deltaTextDataSet(){
        return [
            [ time(), '1min ago'],
            [ (time() - self::HOUR), '1hr ago'],
            [ (time() - self::HOUR * 3), '3hr ago']
        ];
    }

    /**
     * Format testing data set
     */
    public function formatDataSet(){
        return [
            [ '%Y-%m-%d', 'Y-m-d'],
            [ '%Y-%m-%d %H:%s', 'Y-m-d H:U'],
            [ '%Y %M', 'Y i']
        ];
    }

    /**
     * Format revers transform testing data set
     */
    public function bkFormatDataSet(){
        return [
            [ 'Y-m-d', '\Y-\m-\d'],
            [ 'Y-m-d H:U', '\Y-\m-\d \H:\U'],
            [ 'Y i', '\Y \i']
        ];
    }
}
