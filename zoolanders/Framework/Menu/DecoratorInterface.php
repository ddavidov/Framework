<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Menu;

interface DecoratorInterface
{
    /**
     * Add item index and level to class attribute
     *
     * @param  \SimpleXMLElement $node The node to add the index and level to
     * @param  array $args Callback arguments
     *
     * @since    2.0
     */
    public function index(\SimpleXMLElement $node, $args);
}