<?php

namespace Zoolanders\Framework\Listener\Environment;

use Zoolanders\Framework\Listener\Listener;

class IncludeAssets extends Listener
{
    /**
     * @param \Zoolanders\Framework\Event\Environment\BeforeRender $event
     */
    public function handle(\Zoolanders\Framework\Event\Environment\BeforeRender $event)
    {
        $this->container->assets->css->load();
        $this->container->assets->js->load();
    }
}