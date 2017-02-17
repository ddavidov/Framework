<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service;

class Data
{
    /**
     * Create a data object
     *
     * @param mixed $data The data to load
     * @param string $format The data format (default: json)
     *
     * @return Data The class representing the data
     *
     * @since 1.0.0
     */
    public function create($data = array(), $format = 'json') {
        $class = '\\Zoolanders\\Framework\\Data\\' . ucfirst(strtolower($format));
        return new $class($data);
    }
}