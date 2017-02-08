<?php
/**
 * Created by PhpStorm.
 * User: skullbock
 * Date: 07/02/17
 * Time: 18:47
 */

namespace Zoolanders\Framework\Migration;

class Config extends \Phinx\Config\Config
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $configArray = [], $configFilePath = null)
    {
        $type = \JFactory::getConfig()->get('dbtype');

        if ($type == 'mysqli') {
            $type = 'mysql';
        }

        // Force the configuration using the joomla data and some standard zl stuff
        $defaultConfigArray = [
            'environments' => [
                'default_migration_table' => \JFactory::getConfig()->get('dbprefix') . 'zoo_zl_migrations',
                'production' => [
                    'name' => \JFactory::getConfig()->get('db'),
                    'user' => \JFactory::getConfig()->get('user'),
                    'pass' => \JFactory::getConfig()->get('password'),
                    'host' => \JFactory::getConfig()->get('host'),
                    'adapter' => $type,
                    'table_prefix' => \JFactory::getConfig()->get('dbprefix') . 'zoo_zl_'
                ]
            ],
            'paths' => [
                'migrations' => dirname(__FILE__) . '/Migrations',
                'seeds' => dirname(__FILE__) . '/Seeds'
            ]
        ];

        $configArray = array_merge($defaultConfigArray, $configArray);

        parent::__construct($configArray, $configFilePath);
    }
}