<?php

namespace Zoolanders\Event\Dispatcher;

use Zoolanders\Event\Event;

abstract class Dispatcher extends Event
{
    /**
     * @var \Zoolanders\Dispatcher\Dispatcher
     */
    protected $dispatcher;

    /**
     * @param \Zoolanders\Dispatcher\Dispatcher $dispatcher
     */
    public function __construct(\Zoolanders\Dispatcher\Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return \Zoolanders\Dispatcher\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}