<?php

namespace Zoolanders\Service;

use Zoolanders\Container\Container;
use Zoolanders\Event\Dispatcher;
use Zoolanders\Event\Environment\Init;
use Zoolanders\Event\Zoo;

class Event extends Service
{
    /**
     * @var Dispatcher
     */
    public $dispatcher;

    /**
     * @var Zoo
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

        // Load every zoolanders plugin by default
        \JPluginHelper::importPlugin('zoolanders');

        // Create the various dispatchers
        $this->dispatcher = new Dispatcher($c);
        $this->joomla = \JEventDispatcher::getInstance();
        $this->zoo = new Zoo($c);
    }
}