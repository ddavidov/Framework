<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Utils;

trait NameFromClass
{
    /**
     * The (base) name of the current class
     *
     * @var    string
     */
    protected $name;

    /**
     * Method to get the model name
     * @return  string  The name of the model
     */
    public function getName()
    {
        if (empty($this->name)) {
            $class = explode("\\", get_class($this));
            $this->name = array_pop($class);
        }

        return $this->name;
    }
}