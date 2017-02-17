<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service;

class Cache
{

    /**
     * Creates an Cache instance
     *
     * @param string $file Path to cache file
     * @param boolean $hash Wether the key should be hashed
     * @param int $lifetime The values lifetime
     *
     * @return \Zoolanders\Framework\Cache\Cache
     */
    public function create($file, $hash = true, $lifetime = null, $type = 'file')
    {
        if ($type == 'apc' && extension_loaded('apc') && class_exists('\\APCIterator')) {
            $cache = new \Zoolanders\Framework\Cache\Apc(md5($file), $lifetime);
        } else {
            $cache = new \Zoolanders\Framework\Cache\Cache($file, $hash, $lifetime);
        }

        return $cache;
    }

}
