<?php

namespace Zoolanders\Framework\Container;

defined('_JEXEC') or die;

class Nested extends Container
{
    /**
     * @var Container
     */
    protected $parentContainer;

    /**
     * @param mixed $container
     */
    public function setParentContainer(Container &$container)
    {
        $this->parentContainer = $container;
    }

    /**
     * If a service is not set here, try with the parent
     * @param string $id
     * @return mixed|void
     */
    public function offsetGet($id)
    {
        try {
            return parent::offsetGet($id);
        } catch (\InvalidArgumentException $e) {
            if ($this->parentContainer) {
                // Proxy it to the parent
                return $this->parentContainer[$id];
            }

            throw $e;
        }
    }
}