<?php

/**
 * Mostly taken from FOF3 View class
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 *
 * Extended by ZOOlanders
 */
namespace Zoolanders\Framework\View;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Event\View\AfterDisplay;
use Zoolanders\Framework\Event\View\BeforeDisplay;
use Zoolanders\Framework\Event\View\GetTemplatePath;
use Zoolanders\Framework\Utils\NameFromClass;

/**
 * Class View
 */
class View
{
    use Triggerable, NameFromClass;

    /**
     * Layout name
     *
     * @var    string
     */
    protected $layout = 'default';

    protected $templatePaths = [];

    /**
     * The container attached to this view
     *
     * @var   Container
     */
    protected $container;

    /**
     * Constructor.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->getName();
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
     * Sets an entire array of search paths for templates or resources.
     *
     * @param   mixed $path The new search path, or an array of search paths.  If null or false, resets to the current
     *                      directory only.
     *
     * @return  void
     */
    protected function setTemplatePath($path)
    {
        // Clear out the prior search dirs
        $this->templatePaths = array();

        // Actually add the user-specified directories
        $this->addTemplatePath($path);

        // Set the alternative template search dir
        $templatePath = JPATH_THEMES;
        $fallback = $templatePath . '/' . $this->container->system->getTemplate() . '/html/com_zoolanders/' . $this->getName();
        $this->addTemplatePath($fallback);

        $this->container->event->dispatcher->trigger(new GetTemplatePath($this));
    }

    /**
     * Adds to the search path for templates and resources.
     *
     * @param   mixed $path The directory or stream, or an array of either, to search.
     *
     * @return  void
     */
    public function addTemplatePath($path)
    {
        // Just force to array
        settype($path, 'array');

        // Loop through the path directories
        foreach ($path as $dir) {
            // No surrounding spaces allowed!
            $dir = trim($dir);

            // Add trailing separators as needed
            if (substr($dir, -1) != DIRECTORY_SEPARATOR) {
                // Directory
                $dir .= DIRECTORY_SEPARATOR;
            }

            // Add to the top of the search dirs
            array_unshift($this->templatePaths, $dir);
        }
    }

    /**
     * Escapes a value for output in a view script.
     *
     * @param   mixed $var The output to escape.
     *
     * @return  mixed  The escaped value.
     */
    public function escape($var)
    {
        return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Method to get data from a registered model or a property of the view
     *
     * @param   string $property The name of the method to call on the Model or the property to get
     * @param   string $default The default value [optional]
     * @param   string $modelName The name of the Model to reference [optional]
     *
     * @return  mixed  The return value of the method
     */
    public function get($property, $default = null, $modelName = null)
    {
        if (@isset($this->$property)) {
            return $this->$property;
        } else {
            return $default;
        }
    }

    /**
     * Overrides the default method to execute and display a template script.
     * Instead of loadTemplate is uses loadAnyTemplate.
     *
     * @param   string $tpl The name of the template file to parse
     *
     * @return  boolean  True on success
     *
     * @throws  \Exception  When the layout file is not found
     */
    public function display($tpl = null)
    {
        $this->triggerEvent(new BeforeDisplay($this, $tpl));

        $templateResult = $this->loadTemplate($tpl);

        echo $templateResult;

        $this->triggerEvent(new AfterDisplay($this, $tpl, $templateResult));

        return true;
    }

    /**
     * Get the layout.
     *
     * @return  string  The layout name
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Sets the layout name to use
     *
     * @param   string $layout The layout name or a string in format <template>:<layout file>
     *
     * @return  $this
     */
    public function setLayout($layout)
    {
        if (is_null($layout)) {
            $layout = 'default';
        }

        if (strpos($layout, ':') === false) {
            $this->layout = $layout;
        } else {
            // Convert parameter to array based on :
            $temp = explode(':', $layout);
            $this->layout = $temp[1];

            // Set layout template
            $this->layoutTemplate = $temp[0];
        }

        return $this;
    }

    /**
     * Loads a template given any path.
     *
     * @param   string $uri The template path
     * @param   array $forceParams A hash array of variables to be extracted in the local scope of the template file
     * @param   callable $callback A method to post-process the evaluated view template
     *
     * @return  string  The output of the template
     *
     * @throws  \Exception  When the layout file is not found
     */
    public function loadTemplate($forceParams = null)
    {
        ob_start();

        // Extract forced parameters
        if (!empty($forceParams)) {
            extract($forceParams);
        }

        include($this->templatePaths[0] . $this->getLayout() . '.php');

        return ob_get_clean();
    }

    /**
     * Returns a reference to the container attached to this View
     *
     * @return Container
     */
    public function &getContainer()
    {
        return $this->container;
    }
}
