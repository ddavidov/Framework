<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Listener;

use Zoolanders\Framework\Container\Container;

interface ListenerInterface
{
    public function handle(\Zoolanders\Framework\Event\EventInterface $event);
}