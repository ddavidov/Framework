<?php

namespace Zoolanders\Zoo;

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
     * @var App
     */
    protected $app;

    /**
     * @var bool
     */
    protected $loaded = false;

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
        return call_user_func_array([$this->app, $name], $arguments);
    }

    /**
     * Proxy also the helpers calls
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->app->$name;
    }

    /**
     * Get the main zoo App class
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Check if zoo is loaded
     * @return boolean
     */
    public function isLoaded()
    {
        return $this->loaded;
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
        require_once(JPATH_SITE . '/administrator/components/com_zoo/config.php');

        $this->app = App::getInstance('zoo');

        // register plugin path
        if ($path = $this->app->path->path('root:plugins/system/zoo_zlelements/zoo_zlelements')) {
            $this->app->path->register($path, 'zlpath');
        }

        // register elements path
        if ($path = $this->app->path->path('zlpath:elements')) {
            $this->app->path->register($path, 'elements');
        }

        // register fields path
        if ($path = $this->app->path->path('zlpath:fields')) {
            $this->app->path->register($path, 'zlfields');
        }

        // register helpers
        if ($path = $this->app->path->path('zlpath:helpers')) {
            $this->app->path->register($path, 'helpers');
            $this->app->loader->register('ZlHelper', 'helpers:zlhelper.php');
        }

        // register plugin path
        if ($path = $this->app->path->path('root:plugins/system/zlframework/zlframework')) {
            $this->app->path->register($path, 'zlfw');
        }

        // register classes
        if ($path = $this->app->path->path('zlfw:classes')) {
            $this->app->path->register($path, 'classes');
            $this->app->loader->register('ZLStorage', 'classes:zlstorage/ZLStorage.php');
        }

        // register elements fields
        if ($path = $this->app->path->path('zlfw:zlfield')) {
            $this->app->path->register($path, 'zlfield'); // used since ZLFW 2.5.8
            $this->app->path->register($path . '/fields/elements', 'zlfields'); // temporal until all ZL Extensions adapted
            $this->app->path->register($path . '/fields/elements', 'fields'); // necessary since ZOO 2.5.13
        }

        // register elements - order is important!
        if ($path = $this->app->path->path('zlfw:elements')) {
            $this->app->path->register($path, 'elements'); // register elements path

            $this->app->loader->register('ElementPro', 'elements:pro/pro.php');
            $this->app->loader->register('ElementRepeatablepro', 'elements:repeatablepro/repeatablepro.php');
            $this->app->loader->register('ElementFilespro', 'elements:filespro/filespro.php');
        }

        if ($path = JPATH_ROOT . '/media/zoo/custom_elements') {
            $this->app->path->register($path, 'elements'); // register custom elements path
        }

        // register helpers
        if ($path = $this->app->path->path('zlfw:helpers')) {
            $this->app->path->register($path, 'helpers');
            $this->app->loader->register('zlfwHelper', 'helpers:zlfwhelper.php');
            $this->app->loader->register('ZLDependencyHelper', 'helpers:zldependency.php');
            $this->app->loader->register('ZlStringHelper', 'helpers:zlstring.php');
            $this->app->loader->register('ZlFilesystemHelper', 'helpers:zlfilesystem.php');
            $this->app->loader->register('ZlPathHelper', 'helpers:zlpath.php');
            $this->app->loader->register('ZlModelHelper', 'helpers:model.php');
            $this->app->loader->register('ZLXmlHelper', 'helpers:zlxmlhelper.php');
            $this->app->loader->register('ZLFieldHTMLHelper', 'helpers:zlfieldhtml.php');
        }

        $this->loaded = true;
    }
}