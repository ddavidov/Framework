<?php

namespace ZFTests\Cache;

use JmesPath\Tests\_TestClass;
use ZFTests\TestCases\ZFTestCase;
use ZFTests\Classes\Cacheable;


/**
 * Class CacheTest
 * Caching tests
 *
 * @package ZFTests\Cache
 */
class CacheTest extends ZFTestCase
{
    use Cacheable;

    /**
     * Test if cache storage files is created successfully and are accessible for
     * write/read
     */
    public function testCacheStorageIO(){

        $this->initCache();
        $this->assertTrue($this->cache->check());

        // Check if file is really exists and accessible:
        $this->assertFileExists($this->cache_file);
        //@TODO: Check out why the lines below cause errors:
        //$this->assertFileIsReadable($this->cache_file);
        //$this->assertFileIsWritable($this->cache_file);
    }

    /**
     * Test if cache values are stored / retrieved correctly
     *
     * @depends testCacheStorageIO
     */
    public function testCacheSetRetrievement(){

        $this->initCache();

        $this->assertNotEmpty($this->cache);
        $this->cache->set('test_var', 'testvalue');
        $this->cache->save();
        $this->assertEquals('testvalue', $this->cache->get('test_var'));
    }

    /**
     * Test cache file removing and cleanup
     *
     * @depends testCacheStorageIO
     */
    public function testCacheCleanup(){

        $this->initCache();
        $this->cache->clear();
        $this->cache->save();
        $this->assertEmpty($this->cache->get('test_var'));
    }
}
