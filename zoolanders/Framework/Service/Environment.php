<?php

namespace Zoolanders\Service;

use Zoolanders\Container\Container;

class Environment extends Service
{
    /**
     * @var
     */
    public $params;

    /**
     * @var string
     */
    protected $environment;

    /**
     * Environment constructor.
     * @param Container|null $container
     */
    public function __construct(Container $container = null)
    {

        // call parent constructor
        parent::__construct($container);

        // set params as DATA class
        $this->params = $container->zoo->getApp()->data->create(array());
    }

    /**
     *
     * returns the current environment
     * Example environments:
     * Item View - site.com_zoo.item
     * Category View - site.com_zoo.category
     * ZOO manager - admin.com_zoo.manager
     *
     * @return string
     */
    public function get()
    {
        if (!$this->$environment) {
            // init vars
            $environment = array();
            $jinput = $this->container->system->application->input;

            $component = $jinput->getCmd('option', null);
            $task = $jinput->getCmd('task', null);
            $view = $jinput->getCmd('view', null);

            // set back or frontend
            $environment[] = $this->container->system->application->isAdmin() ? 'admin' : 'site';

            // set component
            $environment[] = $component;

            // set controller
            $environment[] = $this->container->zoo->getApp()->request->getCmd('controller', null);

            // if ZOO
            if ($component == 'com_zoo') {
                // if zoo item full view
                if ($task == 'item') {
                    $environment[] = 'item';
                    $this->params->set('item_id', $this->container->zoo->getApp()->request->getCmd('item_id'));
                    unset($task);
                } else if ($view == 'item') { // if joomla item menu route
                    $environment[] = 'item';

                    if ($item_id = $this->container->zoo->getApp()->request->getInt('item_id')) {
                        $this->params->set('item_id', $item_id);
                    } elseif ($menu = $this->container->zoo->getApp()->menu->getActive()) {
                        $this->params->set('item_id', $menu->params->get('item_id'));
                    }

                    unset($view);
                } // if zoo cat
                else if ($task == 'category') {
                    $environment[] = 'category';
                    $this->params->set('category_id', $this->container->zoo->getApp()->request->getCmd('category_id'));
                    unset($task);
                } else if ($view == 'category') { // if joomla item menu route
                    $environment[] = 'category';

                    if ($menu = $this->container->zoo->getApp()->menu->getActive()) {
                        $this->params->set('category_id', $menu->params->get('category'));
                    }
                    unset($view);
                }
            }

            // add task/view to the environment
            if (isset($task) && !empty($task)) $environment[] = $task;
            else if (isset($view) && !empty($view)) $environment[] = $view;

            // clean values
            $environment = array_filter($environment);

            // return result in point format
            $this->environment = implode('.', $environment);
        }

        return $this->environment;
    }

    /**
     * Checks if the passed environment is the current environment
     *
     * @param $environments        string|array    array of or string separated by space of environments to check
     * @return boolean
     */
    public function is($environments = [])
    {
        if (!is_array($environments)) {
            // multiple environments posible
            $environments = explode(' ', $environments);
        }

        foreach ($environments as $env) {
            // if in any environment, return true
            if (strpos($this->get(), trim($env)) === 0) {
                return true;
                break;
            }
        }

        return false;
    }
}