<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Menu;

/**
 * Represents a Menu Divider
 */
class Divider extends Item
{

    /**
     * Render the single menu item
     *
     * @return string The html for this menu item
     */
    public function render()
    {
        return '<li class="uk-nav-divider"></li>';
    }
}
