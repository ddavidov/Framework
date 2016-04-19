<?php

namespace Zoolanders\Event;

class ContainerServicesLoaded extends Event
{
    /**
     * @var object
     */
    protected $services;

    /**
     * ContainerServicesLoaded constructor.
     * @param mixed $services
     */
    public function __construct($services)
    {
        $this->services = $services;
    }
}