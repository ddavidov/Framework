<?php

namespace Zoolanders\Event\Controller;

use Zoolanders\Event\Event;

abstract class Controller extends Event
{
    /**
     * @var \Zoolanders\Controller\Controller
     */
    protected $controller;

    /**
     * BeforeExecute constructor.
     * @param \Zoolanders\Controller\Controller $controller
     */
    public function __construct(\Zoolanders\Controller\Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return \Zoolanders\Controller\Controller
     */
    public function getController()
    {
        return $this->controller;
    }
}