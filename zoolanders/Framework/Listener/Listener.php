<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Listener;

use Zoolanders\Framework\Container\Container;

abstract class Listener
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Listener constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->container = $c;
    }
}