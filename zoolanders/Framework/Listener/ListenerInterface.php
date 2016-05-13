<?php

namespace Zoolanders\Framework\Listener;

use Zoolanders\Framework\Container\Container;

interface ListenerInterface
{
    public function handle(\Zoolanders\Framework\Event\EventInterface $event);
}