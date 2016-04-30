<?php

namespace Zoolanders\Listener\Environment;

use Zoolanders\Listener\Listener;

class LoadSeparatorAssets extends Listener
{
    /**
     * @param \Zoolanders\Event\Environment\Init $event
     */
    public function handle(\Zoolanders\Event\Environment\Init $event)
    {
        // perform admin tasks
        if ($event->is('zoo-type')) {
            $this->container->document->addStylesheet('elements:separator/assets/zlfield.css');
            $this->container->document->addScript('elements:separator/assets/zlfield.min.js');
            $this->container->document->addScriptDeclaration('jQuery(function($) { $("body").ZOOtoolsSeparatorZLField({ enviroment: "' . $event->get() . '" }) });');
        }
    }
}