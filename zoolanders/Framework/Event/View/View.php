<?php

namespace Zoolanders\Event\View;

use Zoolanders\Event\Event;

class View extends Event
{
    protected $view;

    /**
     * GetTemplatePath constructor.
     * @param $view
     */
    public function __construct(\Zoolanders\View\View $view)
    {
        $this->view = $view;
    }

    /**
     * @return \Zoolanders\View\View
     */
    public function getView()
    {
        return $this->view;
    }
}