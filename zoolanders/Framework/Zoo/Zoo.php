<?php

namespace Zoolanders;

use Zoolanders\Container\Container;
use App;

defined('_JEXEC') or die;

class Zoo
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Zoo Service constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->container = $c;

        // Autoload zoo
        $this->load();
    }

    /**
     * Proxy the call to the app itself
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getApp(), $name], $arguments);
    }

    /**
     * Get the main zoo App class
     * @return App
     */
    public function getApp()
    {
        return App::getInstance('zoo');
    }

    /**
     * Check if zoo is loaded
     * @return boolean
     */
    public function isLoaded()
    {
        return class_exists('\\App');
    }

    /**
     * Load ZOO and its dependencies
     */
    public function load()
    {
        if ($this->isLoaded()) {
            return;
        }

        // load zoo
        require_once($this->container->platform->getPlatformBaseDirs()['public'] . '/administrator/components/com_zoo/config.php');

        $zoo = $this->getApp();

        // register plugin path
        if ($path = $zoo->path->path('root:plugins/system/zoo_zlelements/zoo_zlelements')) {
            $zoo->path->register($path, 'zlpath');
        }

        // register elements path
        if ($path = $zoo->path->path('zlpath:elements')) {
            $zoo->path->register($path, 'elements');
        }

        // register fields path
        if ($path = $zoo->path->path('zlpath:fields')) {
            $zoo->path->register($path, 'zlfields');
        }

        // register helpers
        if ($path = $zoo->path->path('zlpath:helpers')) {
            $zoo->path->register($path, 'helpers');
            $zoo->loader->register('ZlHelper', 'helpers:zlhelper.php');
        }

        // register plugin path
        if ($path = $zoo->path->path('root:plugins/system/zlframework/zlframework')) {
            $zoo->path->register($path, 'zlfw');
        }

        // register classes
        if ($path = $zoo->path->path('zlfw:classes')) {
            $zoo->path->register($path, 'classes');
            $zoo->loader->register('ZLStorage', 'classes:zlstorage/ZLStorage.php');
        }

        // register elements fields
        if ($path = $zoo->path->path('zlfw:zlfield')) {
            $zoo->path->register($path, 'zlfield'); // used since ZLFW 2.5.8
            $zoo->path->register($path . '/fields/elements', 'zlfields'); // temporal until all ZL Extensions adapted
            $zoo->path->register($path . '/fields/elements', 'fields'); // necessary since ZOO 2.5.13
        }

        // register elements - order is important!
        if ($path = $zoo->path->path('zlfw:elements')) {
            $zoo->path->register($path, 'elements'); // register elements path

            $zoo->loader->register('ElementPro', 'elements:pro/pro.php');
            $zoo->loader->register('ElementRepeatablepro', 'elements:repeatablepro/repeatablepro.php');
            $zoo->loader->register('ElementFilespro', 'elements:filespro/filespro.php');
        }

        if ($path = JPATH_ROOT . '/media/zoo/custom_elements') {
            $zoo->path->register($path, 'elements'); // register custom elements path
        }

        // register helpers
        if ($path = $zoo->path->path('zlfw:helpers')) {
            $zoo->path->register($path, 'helpers');
            $zoo->loader->register('zlfwHelper', 'helpers:zlfwhelper.php');
            $zoo->loader->register('ZLDependencyHelper', 'helpers:zldependency.php');
            $zoo->loader->register('ZlStringHelper', 'helpers:zlstring.php');
            $zoo->loader->register('ZlFilesystemHelper', 'helpers:zlfilesystem.php');
            $zoo->loader->register('ZlPathHelper', 'helpers:zlpath.php');
            $zoo->loader->register('ZlModelHelper', 'helpers:model.php');
            $zoo->loader->register('ZLXmlHelper', 'helpers:zlxmlhelper.php');
            $zoo->loader->register('ZLFieldHTMLHelper', 'helpers:zlfieldhtml.php');
        }
    }
}