<?php

namespace Zoolanders\Event\Controller;

class AfterExecute extends Controller
{
    /**
     * @var string
     */
    protected $task;

    /**
     * AfterExecute constructor.
     * @param \Zoolanders\Controller\Controller $controller
     * @param $task
     */
    public function __construct(\Zoolanders\Controller\Controller $controller, $task)
    {
        $this->controller = $controller;
        $this->task = $task;
    }
    
    /**
     * @return string
     */
    public function getTask()
    {
        return $this->task;
    }
}