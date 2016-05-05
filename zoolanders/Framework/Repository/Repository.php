<?php

namespace Zoolanders\Repository;

use Zoolanders\Container\Container;

abstract class Repository implements RepositoryInterface
{
    public function __get($name)
    {
        if ($name == 'container') {
            return Container::getInstance();
        }
    }
}