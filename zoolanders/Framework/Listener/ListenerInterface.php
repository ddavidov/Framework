<?php

namespace Zoolanders\Listener;

use Zoolanders\Container\Container;

interface ListenerInterface
{
    public function handle(\Zoolanders\Event\EventInterface $event);
}