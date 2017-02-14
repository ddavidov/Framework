<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Event;

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