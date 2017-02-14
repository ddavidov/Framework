<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Listener\Type;

use Zoolanders\Framework\Listener\Listener;

class AddCoreElements extends Listener
{
    /**
     * @param \Zoolanders\Framework\Event\Type\Coreconfig $event
     */
    public function handle(\Zoolanders\Framework\Event\Type\Coreconfig $event)
    {
        $config = $event->getConfig();

        $config['_itemlinkpro'] = array('name' => 'Item Link Pro', 'type' => 'itemlinkpro');
        $config['_staticcontent'] = array('name' => 'Static Content', 'type' => 'staticcontent');

        $event->setConfig($config);
    }
}