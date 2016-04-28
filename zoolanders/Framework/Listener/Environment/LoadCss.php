<?php

namespace Zoolanders\Listener\Environment;

use Zoolanders\Listener\Listener;

class LoadCss extends Listener
{
    /**
     * @param \Zoolanders\Event\Environment\Init $event
     */
    public function handle(\Zoolanders\Event\Environment\Init $event)
    {
        // perform admin tasks
        if ($event->isAdmin()) {
            $this->container->system->document->addStylesheet('zlfw:assets/css/zl_ui.css');
        }
    }
}