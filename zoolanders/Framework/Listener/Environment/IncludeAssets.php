<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Listener\Environment;

use Zoolanders\Framework\Listener\Listener;

class IncludeAssets extends Listener
{
    /**
     * @param \Zoolanders\Framework\Event\Environment\BeforeRender $event
     */
    public function handle(\Zoolanders\Framework\Event\Environment\BeforeRender $event)
    {
        $this->container->assets->css->load();
        $this->container->assets->js->load();
    }
}