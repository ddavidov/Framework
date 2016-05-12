<?php

namespace Zoolanders\Listener\Environment;

use Zoolanders\Listener\Listener;

class IncludeAssets extends Listener
{
    /**
     * @param \Zoolanders\Event\Environment\BeforeRender $event
     */
    public function handle(\Zoolanders\Event\Environment\BeforeRender $event)
    {
        $this->container->assets->css->load();
        $this->container->assets->js->load();
    }
}