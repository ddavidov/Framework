<?php

namespace Zoolanders\Listener;

use Zoolanders\Container\Container;

abstract class Listener
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Listener constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->container = $c;
    }
}