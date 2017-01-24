<?php

namespace Zoolanders\Framework\Event;

use Zoolanders\Framework\Container\Container;

class Zoo extends \Zoolanders\Framework\Service\Service
{
    /**
     * @var \EventHelper
     */
    public $zoo;

    /**
     * Event constructor.
     * @param Container|null $c
     */
    public function __construct(Container $c = null)
    {
        parent::__construct($c);

        $this->zoo = $this->container->zoo->event;

        $this->proxyAllZooEvents();
    }

    /**
     * Proxy any zoo event to a zoolanders event
     */
    protected function proxyAllZooEvents()
    {
        // first, get any know zoo event
        $zooDispatcher = new \ReflectionClass($this->zoo->dispatcher);
        $property =  $zooDispatcher->getProperty('listeners');
        $property->setAccessible(true);
        $listeners = $property->getValue($this->zoo->dispatcher);

        // Some extra events
        $listeners['item:save'] = [];
        $listeners['type:coreconfig'] = [];

        // Get the event name already registered in the zoo dispatcher
        foreach ($listeners as $eventName => $methods) {

            // proxy each event found
            $eventClass = $this->getEventObjectClass($eventName);

            if (class_exists($eventClass)) {
                $this->zoo->dispatcher->connect($eventName, function ($zooEvent) use ($eventClass) {
                    $event = $this->createEventObject($eventClass, $zooEvent);

                    if ($event) {
                        $event->setReturnValue($zooEvent->getReturnValue());
                        $this->container->event->dispatcher->trigger($event);
                        $zooEvent->setReturnValue($event->getReturnValue());
                    }
                });
            }
        }
    }

    /**
     * @param $eventName
     * @return string
     */
    protected function getEventObjectClass($eventName)
    {
        // Separate resource from method
        $parts = explode(":", $eventName);
        $resource = @$parts[0];
        $event = @$parts[1];

        // First try a dedicated event class for this resource
        $eventClass = '\\Zoolanders\\Event\\' . ucfirst(strtolower($resource)) . '\\' . ucfirst(strtolower($event));

        return $eventClass;
    }

    /**
     * Create the right Zoolanders event class instance from the zoo event
     * @param string $eventClass class to instantiate
     * @param \AppEvent $zooEvent The event itself
     * @return \Zoolanders\Framework\Event\Event
     */
    protected function createEventObject($eventClass, \AppEvent $zooEvent)
    {
        $r = new \ReflectionClass($eventClass);

        // Create the list of the constructor arguments for the event class
        $parameters = [];
        $parameters[] = $zooEvent->getSubject();

        // add any other paramenter
        $parameters = array_merge($parameters, array_values($zooEvent->getParameters()));

        $obj = $r->newInstanceArgs($parameters);

        return $obj;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->zoo->dispatcher, $name], $arguments);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name == 'dispatcher') {
            return $this->zoo->$name;
        } else {
            return $this->zoo->dispatcher->$name;
        }
    }
}
