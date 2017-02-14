<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Listener\Item;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Item;
use Zoolanders\Framework\Item\Indexer;
use Zoolanders\Framework\Listener\Listener;

class IndexSearchValues extends Listener
{
    /**
     * @var Indexer
     */
    protected $indexer;

    /**
     * IndexSearchValues constructor.
     */
    function __construct(Container $c, Indexer $indexer)
    {
        parent::__construct($c);

        $this->indexer = $indexer;
    }

    /**
     * @param Item\Saved $event
     */
    public function handle(Item\Saved $event)
    {
        $item = $event->getItem();
        $this->indexer->index($item);
    }
}