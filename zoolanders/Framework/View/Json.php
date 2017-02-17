<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\View;

/**
 * Class Html
 * @package Zoolanders\Framework\View
 */
class Json extends View
{
    protected $type = 'json';

    /**
     * @inheritdoc
     */
    public function render($data = [])
    {
        if (!empty($data)) {
            $this->data = $data;
        }

        return $this->data;
    }
}
