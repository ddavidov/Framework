<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Response;

/**
 * Class HtmlResponse
 * @package Zoolanders\Framework\Response
 */
class HtmlResponse extends Response
{
    /**
     * @inheritdoc
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();

        return;
    }
}
