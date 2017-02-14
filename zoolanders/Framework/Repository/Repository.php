<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Repository;

use Zoolanders\Framework\Container\Container;

abstract class Repository implements RepositoryInterface
{
    public function __get($name)
    {
        if ($name == 'container') {
            return Container::getInstance();
        }
    }
}