<?php

namespace Zoolanders\Listener\Type;

use Zoolanders\Listener\Listener;

class AfterDisplay extends Listener
{
    /**
     * @param \Zoolanders\Event\View\AfterDisplay $event
     */
    public function handle(\Zoolanders\Event\View\AfterDisplay $event)
    {
        $this->container->assets->css->dump();
        $this->container->assets->js->dump();
    }
}