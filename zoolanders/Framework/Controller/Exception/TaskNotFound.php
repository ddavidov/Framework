<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Controller\Exception;

/**
 * Exception thrown when we can't find a suitable method to handle the requested task
 */
class TaskNotFound extends \InvalidArgumentException {}