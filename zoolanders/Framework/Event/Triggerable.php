<?php

namespace Zoolanders\Event;

trait Triggerable
{
    public function triggerEvent(EventInterface $event)
    {
        $eventName = 'on' . $event->getName();

        // let's try locally
        if (method_exists($this, $eventName)) {
            $this->$eventName($event);
        }

        // First, trigger the joomla event
        $this->container->event->joomla->trigger($eventName, [&$event]);

        // Then trigger also the zoolanders one
        $this->container->event->dispatcher->trigger($event);
    }
}