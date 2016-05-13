<?php

namespace Zoolanders\Framework\Event;
use Zoolanders\Framework\Container\Container;

/**
 * Handles the dispatching of the Event events
 * Cloned from the ZOO EventDispatcher class (Copyright Yootheme)
 */
class Dispatcher
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Dispatcher constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * The listeners for the events
     *
     * @var array
     * @since 1.0.0
     */
    protected $listeners = array();

    /**
     * Connects a listener to a given event name.
     *
     * @param string $name An event name
     * @param mixed $listener A PHP callable
     *
     * @since 1.0.0
     */
    public function connect($name, $listener)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = array();
        }

        $this->listeners[$name][] = $listener;
    }

    /**
     * Disconnects a listener for a given event name.
     *
     * @param string $name An event name
     * @param mixed $listener A PHP callable
     *
     * @return mixed false if listener does not exist, null otherwise
     *
     * @since 1.0.0
     */
    public function disconnect($name, $listener)
    {
        if (!isset($this->listeners[$name])) {
            return false;
        }

        foreach ($this->listeners[$name] as $i => $callable) {
            if ($listener === $callable) {
                unset($this->listeners[$name][$i]);
            }
        }

        // also, disconnect it to the core zoo listeners to keep b/c
        $this->container->event->zoo->disconnect($name, $listener);
    }

    /**
     * @see notify
     */
    public function trigger(Event $event)
    {
        return $this->notify($event);
    }

    /**
     * Notifies all listeners of a given event.
     *
     * @param Event $event The event
     *
     * @return Event The Event instance
     *
     * @since 1.0.0
     */
    public function notify(Event $event)
    {
        foreach ($this->getListeners($event->getName()) as $listener) {
            call_user_func_array($listener, [$event]);
        }

        return $event;
    }

    /**
     * Returns true if the given event name has some listeners.
     *
     * @param string $name The event name
     *
     * @return boolean true if some listeners are connected, false otherwise
     *
     * @since 1.0.0
     */
    public function hasListeners($name)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = array();
        }

        // Both local and zoo's
        return (boolean) count($this->listeners[$name]);
    }

    /**
     * Returns all listeners associated with a given event name.
     *
     * @param string $name The event name
     *
     * @return array An array of listeners
     *
     * @since 1.0.0
     */
    public function getListeners($name)
    {
        if (!isset($this->listeners[$name])) {
            return array();
        }

        // merge ours with zoo's
        return $this->listeners[$name];
    }

}