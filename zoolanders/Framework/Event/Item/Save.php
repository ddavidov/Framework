<?php

namespace Zoolanders\Event\Item;

class Save extends \Zoolanders\Event\Event
{
    /**
     * @var \Item
     */
    protected $item;

    /**
     * Beforesave constructor.
     * @param \Item $item
     */
    public function __construct(\Item $item)
    {
        $this->item = $item;
    }

    /**
     * @return \Item
     */
    public function getItem()
    {
        return $this->item;
    }
}
