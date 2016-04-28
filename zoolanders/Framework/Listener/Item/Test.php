<?php

namespace Zoolanders\Listener\Item;

use Zoolanders\Listener\Listener;

class Test extends Listener
{
    /**
     * @param \Zoolanders\Event\Item\Save $event
     */
    public function handle(\Zoolanders\Event\Item\Save $event)
    {
        $event->getItem()->name = 'Super Test';
    }
}