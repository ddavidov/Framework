<?php

namespace Zoolanders\Framework\Listener\Item;

use Zoolanders\Framework\Listener\Listener;

class Test extends Listener
{
    /**
     * @param \Zoolanders\Framework\Event\Item\Save $event
     */
    public function handle(\Zoolanders\Framework\Event\Item\Save $event)
    {
        $event->getItem()->name = 'Super Test';
    }
}