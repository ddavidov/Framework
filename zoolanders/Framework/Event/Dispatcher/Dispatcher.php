<?php

namespace Zoolanders\Framework\Event\Dispatcher;

use Zoolanders\Framework\Event\Event;

abstract class Dispatcher extends Event
{
    /**
     * @var \Zoolanders\Framework\Dispatcher\Dispatcher
     */
    protected $dispatcher;

    /**
     * @param \Zoolanders\Framework\Dispatcher\Dispatcher $dispatcher
     */
    public function __construct(\Zoolanders\Framework\Dispatcher\Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return \Zoolanders\Framework\Dispatcher\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}