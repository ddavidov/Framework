<?php

namespace Zoolanders\Listener\Item;

class Test
{
    /**
     * @param \Zoolanders\Event\Item\Save $event
     */
    public function handle(\Zoolanders\Event\Item\Save $event)
    {
        $event->getItem()->name = 'Super Test';
    }
}