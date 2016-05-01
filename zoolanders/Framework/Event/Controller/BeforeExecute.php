<?php

namespace Zoolanders\Event\Controller;

class BeforeExecute extends Controller
{
    /**
     * @var string
     */
    protected $task;

    /**
     * BeforeExecute constructor.
     * @param \Zoolanders\Controller\Controller $controller
     * @param $task
     */
    public function __construct(\Zoolanders\Controller\Controller $controller, &$task)
    {
        $this->controller = $controller;
        $this->task = &$task;
    }
    
    /**
     * @return string
     */
    public function &getTask()
    {
        return $this->task;
    }
}