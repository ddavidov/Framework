<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Response;

/**
 * Interface ResponseInterface
 * @package Zoolanders\Framework\Response
 */
interface ResponseInterface
{
    /**
     * Set response header
     *
     * @param   $key
     * @param   $value
     *
     * @return  Response
     */
    public function setHeader($key, $value);

    /**
     * Set content
     *
     * @param   Content
     *
     * @return  void
     */
    public function setContent($content);

    /**
     * Send prepared response to user agent
     *
     * @return  mixed
     */
    public function send();
}
