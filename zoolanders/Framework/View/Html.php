<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\View;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\View\GetTemplatePath;

/**
 * Class Html
 * @package Zoolanders\Framework\View
 */
class Html extends View
{
    /**
     * @var string
     */
    protected $type = 'html';

    /**
     * Layout name
     *
     * @var    string
     */
    protected $layout = 'default';

    /**
     * List of paths where to find templates
     *
     * @var array
     */
    protected $templatePaths = [];

    /**
     * HtmlView constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->addTemplatePath(JPATH_COMPONENT . '/View/' . ucfirst($this->getName()) . '/tmpl');
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
     * @param string $tpl The layout name
     *
     * @return  string  The output of the template
     *
     * @throws  \Exception  When the layout file is not found
     */
    public function loadTemplate($tpl = null)
    {
        $tpl = empty($tpl) ? $this->getLayout() : $tpl;

        ob_start();

        // Extract forced parameters
        if (!empty($this->data)) {
            extract($this->data);
        }

        include($this->templatePaths[0] . $tpl . '.php');

        return ob_get_clean();
    }

    /**
     * @param null $tpl
     * @param array $data
     * @return string
     */
    public function render($tpl = null, $data = [])
    {
        if(!empty($data)){
            $this->data = $data;
        }

        $templateResult = $this->loadTemplate($tpl);

        return $templateResult;
    }
}
