<?php

namespace Zoolanders\Service;

use Zoolanders\Container\Container;

class Joomla extends Service
{
    /**
     * The current joomla version
     *
     * @var \JVersion
     * @since 1.0.0
     */
    public $version;

    /**
     * Class Constructor
     *
     * @param App $app A reference to the global app object
     */
    public function __construct(Container $c)
    {
        parent::__construct($c);

        \JLoader::import('joomla.version');

        $this->version = new \JVersion();
    }

    /**
     * Get the current Joomla installation short version (i.e: 2.5.3)
     *
     * @return string The short version of joomla (ie: 2.5.3)
     *
     * @since 1.0.0
     */
    public function getVersion()
    {
        return $this->version->getShortVersion();
    }

    /**
     * Check the current version of Joomla
     *
     * @param string $version The version to check
     * @param boolean $release Compare only release versions (2.5 vs 2.5 even if 2.5.6 != 2.5.3)
     *
     * @return boolean If the version of Joomla is equal of the one passed
     *
     * @since 1.0.0
     */
    public function isVersion($version, $release = true)
    {
        return $release ? $this->version->RELEASE == $version : $this->getVersion() == $version;
    }

    /**
     * Get the default access group
     *
     * @return int The default group id
     *
     * @since 1.0.0
     */
    public function getDefaultAccess()
    {
        return $this->container->system->config->get('access');
    }
}
