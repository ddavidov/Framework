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
use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Response\ResponseInterface;
use Zoolanders\Framework\Service\Environment;
use Zoolanders\Framework\Service\Event;

class Dispatcher
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Dispatcher constructor.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return ResponseInterface
     */
    public function dispatch(Request $request)
    {
        $controller = $this->container->factory->controller($request);

        if (!$controller) {
            throw new Exception\ControllerNotFound();
        }

        $response = $this->container->execute([$controller, $request->getCmd('task', $controller->getDefaultTask())]);

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        $content = $response;
        $view = $this->container->factory->view($request);

        $response = $this->container->factory->response($request);
        $response->setContent($view->render($content));

        return $response;
    }
}