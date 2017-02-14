<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Event;

interface EventInterface
{
    public function getName();

    public function getProperties();

    public function setReturnValue($value);

    public function getReturnValue();
}