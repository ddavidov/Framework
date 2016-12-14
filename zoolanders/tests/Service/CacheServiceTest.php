<?php

namespace ZFTests\Service;

use Zoolanders\Framework\Service\Cache as CacheService;
use Zoolanders\Framework\Service\Filesystem;

/**
 * Class CacheServiceTest
 * Cache service tests
 *
 * @package ZFTests\Service
 */
class CacheServiceTest extends ServiceTest
{
    /**
     * Test create cache
     *
     * @covers          CacheService::create()
     */
    public function testCreate(){
        $filename = FIXTURES_PATH . '/cache/testcache';
        $cache = new CacheService(self::$container);
        $fs = new Filesystem(self::$container);

        $cache->create($filename);
        $this->assertFileExists($filename);

        $fs->delete($filename);
        $this->assertFileNotExists($filename);
    }
}
