<?php

namespace ZFTests\Service\Filesystem;

use ZFTests\TestCases\ZFTestCase;
use ZFTests\Classes\Filesystem;

/**
 * Class FilesystemServiceTest
 * Filesystem service traits and classes test
 *
 * @package ZFTests\Service\Filesystem
 */
class FilesystemServiceTest extends ZFTestCase
{
    /**
     * Test formating filesize
     *
     * @dataProvider    formatDataSet
     */
    public function testFormatSize($actual, $format, $expected){
        $file = new Filesystem(self::$container);
        $this->assertEquals($expected, $file->formatFilesize($actual, $format));
    }

    /**
     * Test filesize detection
     *
     * @depends         testFormatSize
     * @dataProvider    precizeFilesizeSet
     */
    public function testGetSize($src, $format, $expected){
        $file = new Filesystem(self::$container);
        $this->assertEquals($expected, $file->getSourceSize($src, $format));
    }

    /**
     * Test getting mimetype from file
     *
     * @dataProvider    difMimeTypesDataSet
     */
    public function testGetMimeType($src, $expected){
        $file = new Filesystem(self::$container);
        $this->assertEquals($expected, $file->getContentType($src));
    }

    /**
     * Test filenames cleanups
     *
     * @dataProvider    filenamesProvider
     */
    public function testMakeSafe($filename, $expected){
        $file = new Filesystem(self::$container);
        $this->assertEquals($expected, $file->makeSafe($filename));
    }

    /**
     * Test filepath cleanups
     *
     * @dataProvider    filepathsProvider
     */
    public function testPathCleanup($src, $expected){
        $file = new Filesystem(self::$container);
        $this->assertEquals($expected, $file->cleanPath($src));
    }

    /**
     * Filesize data provider based on simple fixtured files
     */
    public function precizeFilesizeSet(){
        return [
           [ 'fixtures/filesystem/test1.txt', true, '13 B' ],
           [ 'fixtures/filesystem/test1.txt', false, 13 ],
           [ 'fixtures/filesystem/test2.txt', false, 10 ],
           [ 'fixtures/filesystem', true, '23 B' ],
        ];
    }

    /**
     * Provides pointers to files with different mimetypes
     */
    public function difMimeTypesDataSet(){
        return [
            ['test1.txt', 'text/plain'],
            ['script.js', 'application/x-javascript'],
            ['style.css', 'text/css'],
            ['samplepath', 'application/octet-stream'],
            ['test.pdf', 'application/pdf'],
        ];
    }

    /**
     * Format data provider
     */
    public function formatDataSet(){
        return [
            [10000, false, '9.77 KB'],
            [10000, 'KB', '9.77 KB'],
            [10000, true, '10000 B'],
            [1048576, 'MB', '1 MB'],
            [1000000, 'MB', '0.95 MB'],
            [1073741824, 'GB', '1 GB'],
            [1073741824, 'MB', '1024 MB'],
            [107374182400, 'TB', '0.1 TB']
        ];
    }

    /**
     * Filenames data provider
     */
    public function filenamesProvider(){
        return [
            ['some filename.txt', 'some_filename.txt'],
            ['~some+=?weird!@#$%^&*()-_filename.php', '~someweird-_filename.php'],
            ['111somefilename.txt', '111somefilename.txt'],
        ];
    }

    /**
     * Weird Filepaths data provider
     */
    public function filepathsProvider(){
        return [
            ['/some/related/path/', '/some/related/path/'],
            ['/some/weird///path.txt', '/some/weird/path.txt'],
        ];
    }
}
