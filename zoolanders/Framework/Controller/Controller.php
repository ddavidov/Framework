<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Controller;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Controller\Exception\TaskNotFound;
use Zoolanders\Framework\Event\Controller\AfterExecute;
use Zoolanders\Framework\Event\Controller\BeforeExecute;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Response\ResponseInterface;
use Zoolanders\Framework\Utils\NameFromClass;
use Zoolanders\Framework\Response\Response;
use Zoolanders\Framework\View\ViewInterface;

/**
 * Class Controller
 * Inspired by FOF3 Controller class by Nicholas K. Dionysopoulos / Akeeba Ltd (https://github.com/akeeba/fof/)
 */
class Controller
{
    use Triggerable, NameFromClass;

    /**
     * Array of class methods
     *
     * @var    array
     */
    protected $methods;

    /**
     * defaultTask
     *
     * @var    string
     */
    protected $defaultTask = 'display';

    /**
     * The current view name; you can override it in the configuration
     *
     * @var string
     */
    protected $view = '';

    /**
     * The current layout; you can override it in the configuration
     *
     * @var string
     */
    protected $layout = null;

    /**
     * The container attached to this Controller
     *
     * @var Container
     */
    protected $container = null;

    /**
     * Controller constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        // Get a local copy of the container
        $this->container = $container;

        $this->registerDefaultTask($this->defaultTask ? $this->defaultTask : 'default');
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
     * Executes a given controller task. The onBefore<task> and onAfter<task>
     * methods are called automatically if they exist.
     *
     * @param   string $task The task to execute, e.g. "browse"
     *
     * @return  null|bool  False on execution failure
     *
     * @throws  TaskNotFound  When the task is not found
     */
    public function execute($task)
    {
        if (empty($task)) {
            $task = $this->defaultTask;
        }

        if (!method_exists($this, $task)) {
            throw new TaskNotFound(\JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $task), 404);
        }

        $this->triggerEvent(new BeforeExecute($this, $task));

        $ret = $this->container->execute([$this, $task]);

        $this->triggerEvent(new AfterExecute($this, $task));

        return $ret;
    }

    /**
     * Default task. Assigns a model to the view and asks the view to render
     * itself.
     *
     * @param   string $tpl The name of the template file to parse
     *
     * @return  ResponseInterface
     */
    public function display($tpl = null)
    {
        $layout = $tpl ? $tpl : $this->getName();

        // Set the layout
        if (!is_null($this->layout)) {
            $layout .= ':' . $this->layout;
        }

        return $this->render($layout);
    }

    /**
     * Returns a named View object
     *
     * @param   string $name The Model name. If null we'll use the modelName
     *                           variable or, if it's empty, the same name as
     *                           the Controller
     * @param   array $config Configuration parameters to the Model. If skipped
     *                           we will use $this->config
     *
     * @return  ViewInterface  The instance of the Model known to this Controller
     */
    public function getView($name = null, $config = array())
    {
        // Use provided or default view
        $viewName = !empty($name) ? $name : $this->getName();
        $config['name'] = $viewName;

        $view = $this->container->factory->view($this->input, $config);

        return $view;
    }

    /**
     * Register the default task to perform if a mapping is not found.
     *
     * @param   string $method The name of the method in the derived class to perform if a named task is not found.
     *
     * @return  Controller  This object to support chaining.
     */
    public function registerDefaultTask($method)
    {
        $this->defaultTask = $method;

        return $this;
    }

    /**
     * Render view with provided data
     *
     * @param   string  Layout name: "viewname:tmplname"
     * @param   mixed   Playload data
     *
     * @return  Response
     */
    protected function render($layout, $data = array())
    {
        $buffer = explode(":", $layout);
        $viewName = array_shift($buffer);
        $tplName = @array_shift($buffer);

        $viewName = empty($viewName) ? $this->getName() : $viewName;
        $response = $this->container->factory->response($this->input);

        $view = $this->getView($viewName);
        $response->setContent($view->display($tplName, $data));

        return $response;
    }
}
