<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

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
use Zoolanders\Framework\Response\Response;

/**
 * Class View
 */
abstract class View implements ViewInterface
{
    use Triggerable, NameFromClass;

    /**
     * @var string  View type
     */
    protected $type = '';

    /**
     * The container attached to this view
     *
     * @var   Container
     */
    protected $container;

    /**
     * Render data
     *
     * @var
     */
    public $data = [];

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
     * Magic method to bind rendering data
     */
    public function __set($varname, $value)
    {
        $this->data[$varname] = $value;
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
     * @param   mixed $data Data to be rendered
     *
     * @return  string  Rendered content
     *
     * @throws  \Exception  When the layout file is not found
     */
    public function display($tpl = null, $data = [])
    {
        $this->triggerEvent(new BeforeDisplay($this, $tpl, $data));

        $result = $this->render($tpl, $data);

        $this->triggerEvent(new AfterDisplay($this, $tpl, $result));

        return $result;
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

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return  $this->type;
    }

    /**
     * @return mixed
     */
    abstract function render($tpl, $data = []);
}
