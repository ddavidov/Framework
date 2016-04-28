<?php

namespace Zoolanders\Listener\Type;

use Zoolanders\Listener\Listener;

class AddCoreElements extends Listener
{
    /**
     * @param \Zoolanders\Event\Type\Coreconfig $event
     */
    public function handle(\Zoolanders\Event\Type\Coreconfig $event)
    {
        $config = $event->getConfig();

        $config['_itemlinkpro'] = array('name' => 'Item Link Pro', 'type' => 'itemlinkpro');
        $config['_staticcontent'] = array('name' => 'Static Content', 'type' => 'staticcontent');

        $event->setConfig($config);
    }
}