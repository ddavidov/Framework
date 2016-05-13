<?php

namespace Zoolanders\Framework\Listener\Environment;

use Zoolanders\Framework\Listener\Listener;

class LoadSeparatorAssets extends Listener
{
    /**
     * @param \Zoolanders\Framework\Event\Environment\Init $event
     */
    public function handle(\Zoolanders\Framework\Event\Environment\Init $event)
    {
        // perform admin tasks
        if ($event->is('zoo-type')) {
            $this->container->document->addStylesheet('elements:separator/assets/zlfield.css');
            $this->container->document->addScript('elements:separator/assets/zlfield.min.js');
            $this->container->document->addScriptDeclaration('jQuery(function($) { $("body").ZOOtoolsSeparatorZLField({ enviroment: "' . $event->get() . '" }) });');
        }
    }
}