<?php

namespace Zoolanders\Event;

use Joomla\Registry\Registry;

class ContainerConfigurationLoaded extends Event
{
    /**
     * @var Registry
     */
    protected $config;

    /**
     * ContainerConfigurationLoaded constructor.
     * @param Registry $config
     */
    public function __construct(Registry $config)
    {
        $this->config = $config;
    }
}