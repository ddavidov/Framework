<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Dispatcher;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Controller\Controller;
use Zoolanders\Framework\Event\Dispatcher\AfterDispatch;
use Zoolanders\Framework\Event\Dispatcher\BeforeDispatch;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Response\ResponseInterface;

class Dispatcher
{
    use Triggerable;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string|null
     */
    protected $controller;

    /**
     * @var string|null
     */
    protected $task;

    /**
     * Dispatcher constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->controller = $this->input->getCmd('controller', $this->input->getCmd('view', null));
        $this->task = $this->input->getCmd('task');
    }

    /**
     * Magic get method. Handles magic properties:
     * $this->input  mapped to $this->container->input
     *
     * @param   string $name The property to fetch
     *
     * @return  mixed|null
     */
    public function __get($name)
    {
        // Handle $this->input
        if ($name == 'input') {
            return $this->container->input;
        }
    }

    /**
     * @return null|string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param null $default
     * @throws Exception\BadResponseType
     * @throws Exception\ControllerNotFound
     */
    public function dispatch($default = null)
    {
        $this->triggerEvent(new BeforeDispatch($this));

        // controller loaded ?
        $controller = $this->controller ? $this->controller : $default;

        $namespaces = [];
        $namespaces[]= Container::FRAMEWORK_NAMESPACE;

        if ($extension = $this->container->environment->currentExtension()) {
            $namespaces = array_merge($this->container->getRegisteredExtensionNamespaces($extension), $namespaces);
        }

        foreach ($namespaces as $namespace) {
            $class = $namespace . 'Controller\\' . ucfirst($controller);

            if (class_exists($class)) {
                // perform the request task
                /** @var Controller $ctrl */
                $ctrl = $this->container->make($class);
                $response = $ctrl->execute($this->task);

                if ($response instanceof ResponseInterface) {
                    $response->send();
                    break;
                } else {
                    throw new Exception\BadResponseType();
                }
            } else {
                throw new Exception\ControllerNotFound($class);
            }
        }

        $this->triggerEvent(new AfterDispatch($this));
    }
}
