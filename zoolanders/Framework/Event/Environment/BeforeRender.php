<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Event\Environment;

use Joomla\Input\Input;
use Zoolanders\Framework\Service\Request;
use Zoolanders\Framework\Service\Zoo;

class BeforeRender extends \Zoolanders\Framework\Event\Event
{
    /**
     * Init constructor.
     *
     */
    public function __construct()
    {
    }
}
