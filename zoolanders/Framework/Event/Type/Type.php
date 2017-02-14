<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Event\Type;

class Type extends \Zoolanders\Framework\Event\Event
{
    /**
     * @var \Type
     */
    protected $type;

    /**
     * Beforesave constructor.
     * @param \Type $type
     */
    public function __construct(\Type $type)
    {
        $this->type = $type;
    }

    /**
     * @return \Type
     */
    public function getType()
    {
        return $this->type;
    }
}
