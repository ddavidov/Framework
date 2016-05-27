<?php

namespace Zoolanders\Framework\Controller;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Controller\Exception\AccessForbidden;
use Zoolanders\Framework\Controller\Exception\TaskNotFound;
use Zoolanders\Framework\Event\Controller\AfterExecute;
use Zoolanders\Framework\Event\Controller\BeforeExecute;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Utils\NameFromClass;

/**
 * Class Controller
 * Heavily based on FOF3 Controller class
 *
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 *
 * Changed by Zoolanders
 */
class Controller
{
    use Triggerable, NameFromClass;

    /**
     * Redirect message.
     *
     * @var    string
     */
    protected $message;

    /**
     * Redirect message type.
     *
     * @var    string
     */
    protected $messageType;

    /**
     * Array of class methods
     *
     * @var    array
     */
    protected $methods;

    /**
     * URL for redirection.
     *
     * @var    string
     */
    protected $redirect;

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

        $this->registerDefaultTask('display');
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
        if (!method_exists($this, $task)) {
            throw new TaskNotFound(\JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $task), 404);
        }

        $this->triggerEvent(new BeforeExecute($this, $task));
        
        if (!method_exists($this, $task)) {
            $task = $this->defaultTask;
        }

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
     * @return  void
     */
    public function display($tpl = null)
    {
       $view = $this->getView();

        // Set the layout
        if (!is_null($this->layout)) {
            $view->setLayout($this->layout);
        }

        // Display without caching
        $view->display($tpl);
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
     * @return  View  The instance of the Model known to this Controller
     */
    public function getView($name = null, $config = array())
    {
        $viewName = '\Zoolanders\Framework\View\View';

        // Get the model's class name
        $view = $this->container->make($viewName);
        
        // set the default paths
        $view->addTemplatePath(JPATH_COMPONENT . '/views/' . $this->getName() . '/tmpl');
        
        return $view;
    }

    /**
     * Redirects the browser or returns false if no redirect is set.
     *
     * @return  boolean  False if no redirect exists.
     */
    public function redirect()
    {
        if ($this->redirect) {
            $app = $this->container->system->getApplication();
            $app->enqueueMessage($this->message, $this->messageType);
            $app->redirect($this->redirect);

            return true;
        }

        return false;
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
     * Sets the internal message that is passed with a redirect
     *
     * @param   string $text Message to display on redirect.
     * @param   string $type Message type. Optional, defaults to 'message'.
     *
     * @return  string  Previous message
     */
    public function setMessage($text, $type = 'message')
    {
        $previous = $this->message;
        $this->message = $text;
        $this->messageType = $type;

        return $previous;
    }

    /**
     * Set a URL for browser redirection.
     *
     * @param   string $url URL to redirect to.
     * @param   string $msg Message to display on redirect. Optional, defaults to value set internally by controller, if any.
     * @param   string $type Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
     *
     * @return  Controller   This object to support chaining.
     */
    public function setRedirect($url, $msg = null, $type = null)
    {
        // Set the redirection
        $this->redirect = $url;

        if ($msg !== null) {
            // Controller may have set this directly
            $this->message = $msg;
        }

        // Ensure the type is not overwritten by a previous call to setMessage.
        if (empty($this->messageType)) {
            $this->messageType = 'message';
        }

        // If the type is explicitly set, set it.
        if (!empty($type)) {
            $this->messageType = $type;
        }

        return $this;
    }

    /**
     * Provides CSRF protection through the forced use of a secure token. If the token doesn't match the one in the
     * session we return false.
     *
     * @return  bool
     *
     * @throws  \Exception
     */
    protected function csrfProtection()
    {

        $hasToken = false;
        $session = $this->container->system->getSession();

        // Joomla! 2.5+ (Platform 12.1+) method
        if (method_exists($session, 'getToken')) {
            $token = $session->getToken();
            $hasToken = $this->input->get($token, false, 'none') == 1;

            if (!$hasToken) {
                $hasToken = $this->input->get('_token', null, 'none') == $token;
            }
        }

        // Joomla! 2.5+ formToken method
        if (!$hasToken) {
            if (method_exists($session, 'getFormToken')) {
                $token = $session->getFormToken();
                $hasToken = $this->input->get($token, false, 'none') == 1;

                if (!$hasToken) {
                    $hasToken = $this->input->get('_token', null, 'none') == $token;
                }
            }
        }

        if (!$hasToken) {
            throw new AccessForbidden(403, \JText::_('COM_ZOOLANDERS_ACCESS_FORBIDDEN'));
        }

        return true;
    }

    /**
     * Returns true if there is a redirect set in the controller
     *
     * @return  boolean
     */
    public function hasRedirect()
    {
        return !empty($this->redirect);
    }
}