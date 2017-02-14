<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Dispatcher;
use Zoolanders\Framework\Event\Environment\Init;
use Zoolanders\Framework\Event\Zoo;

class Event extends Service
{
    /**
     * @var Dispatcher
     */
    public $dispatcher;

    /**
     * @var Zoo
     */
    public $zoo;

    /**
     * @var \JEventDispatcher
     */
    public $joomla;

    /**
     * Event constructor.
     * @param Container|null $c
     */
    public function __construct(Container $c = null)
    {
        parent::__construct($c);

        // Load every zoolanders plugin by default
        \JPluginHelper::importPlugin('zoolanders');

        // Create the various dispatchers
        $this->dispatcher = new Dispatcher($c);
        $this->joomla = \JEventDispatcher::getInstance();
        $this->zoo = new Zoo($c);
    }
}