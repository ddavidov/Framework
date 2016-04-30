<?php

namespace Zoolanders\Service;

use Zoolanders\Container\Container;

defined('_JEXEC') or die;

/**
 * A helper class to quickly get the component parameters
 */
class Params
{

    /**
     * @var  Container
     */
    protected $container;

    /**
     * Params indexed by component
     *
     * @var array of \Joomla\Registry\Registry
     */
    private $params = [];

    /**
     * Public constructor for the params object
     *
     * @param  Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->reload();
    }

    /**
     * Reload the params
     */
    public function reload($component = 'com_zoo')
    {
        $db = $this->container->db;

        $sql = $db->getQuery(true)
            ->select($db->qn('params'))
            ->from($db->qn('#__extensions'))
            ->where($db->qn('type') . " = " . $db->q('component'))
            ->where($db->qn('element') . " = " . $db->q($component));

        $json = $db->setQuery($sql)->loadResult();

        $this->params[$component] = new \Joomla\Registry\Registry($json);
    }

    /**
     * Returns the value of a component configuration parameter
     *
     * @param   string $key The parameter to get
     * @param   mixed $default Default value
     *
     * @return  mixed
     */
    public function get($key, $default = null, $component = 'com_zoo')
    {
        if (!isset($this->params[$component])) {
            $this->reload($component);
        }

        return $this->params[$component]->get($key, $default);
    }

    /**
     * Returns a copy of the loaded component parameters as an array
     *
     * @return  array
     */
    public function getParams($component = 'com_zoo')
    {
        if (!isset($this->params[$component])) {
            $this->reload($component);
        }

        return $this->params->toArray();
    }

    /**
     * Sets the value of a component configuration parameter
     *
     * @param   string $key The parameter to set
     * @param   mixed $value The value to set
     *
     * @return  void
     */
    public function set($key, $value, $component = 'com_zoo')
    {
        $this->setParams(array($key => $value), $component);
    }

    /**
     * Sets the value of multiple component configuration parameters at once
     *
     * @param   array $params The parameters to set
     *
     * @return  void
     */
    public function setParams(array $params, $component = 'com_zoo')
    {
        if (!isset($this->params[$component])) {
            $this->reload($component);
        }

        foreach ($params as $key => $value) {
            $this->params[$component]->set($key, $value);
        }
    }

    /**
     * Actually Save the params into the db
     */
    public function save()
    {
        $db = $this->container->db;
        foreach ($this->params as $component => $params) {
            $data = $params->toString();

            $sql = $db->getQuery(true)
                ->update($db->qn('#__extensions'))
                ->set($db->qn('params') . ' = ' . $db->q($data))
                ->where($db->qn('element') . ' = ' . $db->q($component))
                ->where($db->qn('type') . ' = ' . $db->q('component'));

            $db->setQuery($sql);

            try {
                $db->execute();
            } catch (\Exception $e) {
                // Don't sweat if it fails
            }
        }
    }
}