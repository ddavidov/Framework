<?php

namespace Zoolanders\Framework\Listener;

use Zoolanders\Framework\Container\Container;

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