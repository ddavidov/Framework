<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\View;

/**
 * Interface ViewInterface
 * @package Zoolanders\Framework\View
 */
interface ViewInterface
{
    /**
     * Render /perform  content method
     *
     * @param null $tpl
     * @param array $data
     *
     * @return mixed
     */
    public function display($tpl = null, $data = []);

    /**
     * Return the view type (html, json, pdf, etc)
     *
     * @return mixed
     */
    public function getType();

    /**
     * @param $tpl
     * @param array $data
     * @return mixed
     */
    public function render($tpl, $data = []);
}
