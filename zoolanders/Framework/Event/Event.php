<?php

namespace Zoolanders\Event;

/**
 * Class Event
 * @package Zoolanders\Event
 */
abstract class Event
{
    /**
     * Get the name of the event
     *
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }
}