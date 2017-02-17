<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Request;

/**
 * Class Request
 * @package Zoolanders\Framework\Request
 */
class Request extends \JInput
{
    /**
     * isAjax
     *
     * @return bool True if an ajax call is being made
     */
    public function isAjax()
    {
        // Joomla way
        if (in_array($this->getCmd('format'), ['json', 'raw'])) {
            return true;
        }

        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
}
