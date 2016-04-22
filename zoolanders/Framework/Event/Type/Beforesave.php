<?php

namespace Zoolanders\Event\Type;

class Beforesave extends \Zoolanders\Event\Event
{
    /**
     * @var \Type
     */
    protected $type;

    /**
     * Beforesave constructor.
     * @param \Type $type
     */
    public function __construct(\Type $type)
    {
        $this->type = $type;
    }

    /**
     * @return \Type
     */
    public function getType()
    {
        return $this->type;
    }
}
