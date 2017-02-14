<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Event\Category;

class Category extends \Zoolanders\Framework\Event\Event
{
    /**
     * @var \Category
     */
    protected $category;

    /**
     * Beforesave constructor.
     * @param \Category $category
     */
    public function __construct(\Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return \Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
