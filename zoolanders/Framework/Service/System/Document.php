<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service\System;

use Zoolanders\Framework\Service\System;

/**
 * Class Document
 * @package Zoolanders\System
 */
class Document extends System
{
    /**
     * Last modification date for the file. Used for prevent the browser to use the cached version of an older CSS file
     *
     * @var string
     * @since 1.0.0
     */
    private $file_mod_date;

    /**
     * Adds a CSS to the document head
     *
     * @param string $path The path to the css file
     * @param string $version A version to add to the url to prevent caching (default: last modification date of the file)
     *
     * @since 1.0.0
     */
    public function addStylesheet($path, $version = null)
    {
        if ($file = $this->container->path->url($path)) {
            $this->getClass()->addStylesheet($file . $this->getVersion($version));
        }
    }

    /**
     * Adds a javascript file to the document head
     *
     * If jquery wasn't loaded yet, load it before the other javascript files
     *
     * @param string $path The path to the javascript file
     * @param string $version A version to add to the url to prevent caching (default: last modification date of the file)
     *
     * @since 1.0.0
     */
    public function addScript($path, $version = null)
    {
        $version = $this->getVersion($version);

        // load jQuery, if not loaded before
        if (!$this->container->joomla->version->isCompatible('3.0')) {
            if (!$this->container->system->application->get('jquery')) {
                $this->container->system->application->set('jquery', true);
                $this->getClass()->addScript($this->container->path->url('libraries:jquery/jquery.js') . $version);
            }
        } else {
            \JHtml::_('jquery.framework');
        }

        if ($file = $this->container->path->url($path)) {
            $this->getClass()->addScript($file . $version);
        }
    }

    /**
     * Get the get parameter to append using the version of a file.
     *
     * If no version is found, check the last time the file was edited and use that date as the version
     *
     * @param string $version The version to append (default: last modification date)
     *
     * @return string The get parameter to append
     */
    private function getVersion($version = null)
    {

        if ($version === null) {
            if (empty($this->file_mod_date)) {
                $this->file_mod_date = date("Ymd", filemtime(__FILE__));
            }

            return '?ver=' . $this->file_mod_date;
        }

        return empty($version) ? '' : '?ver=' . $version;
    }
}