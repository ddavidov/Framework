<?php

namespace Zoolanders\Service;

use Zoolanders\Container\Container;
use Zoolanders\Event\Dispatcher;

class Event extends Service
{
    /**
     * @var Dispatcher
     */
    public $dispatcher;

    /**
     * @var \EventHelper
     */
    public $zoo;

    /**
     * @var \JEventDispatcher
     */
    public $joomla;

    /**
     * Event constructor.
     * @param Container|null $c
     */
    public function __construct(Container $c = null)
    {
        parent::__construct($c);

        // Create the various dispatchers
        $this->dispatcher = new Dispatcher($c);
        $this->joomla = \JEventDispatcher::getInstance();
        $this->zoo = $this->container->zoo->getApp()->event;
    }
}