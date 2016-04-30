<?php

namespace Zoolanders\Cache;

interface CacheInterface
{
    public function check();
    public function get($key);
    public function set($key, $value);
    public function save();
    public function clear();
}
