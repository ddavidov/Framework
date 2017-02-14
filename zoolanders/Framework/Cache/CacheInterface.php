<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Cache;

interface CacheInterface
{
    public function check();
    public function get($key);
    public function set($key, $value);
    public function save();
    public function clear();
}
