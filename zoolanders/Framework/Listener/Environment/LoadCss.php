<?php

namespace Zoolanders\Framework\Listener\Environment;

use Zoolanders\Framework\Listener\Listener;

class LoadCss extends Listener
{
    /**
     * @param \Zoolanders\Framework\Event\Environment\Init $event
     */
    public function handle(\Zoolanders\Framework\Event\Environment\Init $event)
    {
        // perform admin tasks
        if ($event->isAdmin()) {
            $this->container->system->document->addStylesheet('zlfw:assets/css/zl_ui.css');
        }
    }
}