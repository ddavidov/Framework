<?php

namespace Zoolanders\Event\Environment;

class Init extends \Zoolanders\Event\Event
{
    /**
     * @var string
     */
    protected $side;

    /**
     * @var string
     */
    protected $component;

    /**
     * @var string
     */
    protected $view;

    /**
     * Init constructor.
     *
     */
    public function __construct($environment)
    {
        // Break into components
        @list($this->side, $this->component, $this->view) = explode(".", $environment);
    }

    /**
     * @return string
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return ($this->getSide() == 'admin');
    }

    /**
     * @return bool
     */
    public function isSite()
    {
        return !$this->isAdmin();
    }

    /**
     * @return string
     */
    public function getSide()
    {
        return $this->side;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }
}
