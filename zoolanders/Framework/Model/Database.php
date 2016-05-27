<?php

namespace Zoolanders\Framework\Model;

use Illuminate\Support\Collection;
use Zoolanders\Framework\Container\Container;

defined('_JEXEC') or die;

abstract class Database extends Model
{
    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var \JDatabaseQuery
     */
    protected $query;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->query = $this->container->db->getQuery(true);

        // Prefix the table if necessary
        $prefix = (strlen($this->tablePrefix) > 0) ? ' AS ' . $this->query->qn($this->tablePrefix) : '';

        // Autoperform SELECT AND FROM statement
        $this->query->from($this->query->qn($this->tableName) . $prefix);
        $this->fields(['*']);
    }

    /**
     * @param array $fields
     * @param null $prefix
     */
    public function fields($fields = ['*'], $prefix = null)
    {
        if ($prefix === null) {
            $prefix = $this->tablePrefix;
        }

        // Prefix and quote name fields
        foreach ($fields as $field) {
            $field = ($field == '*') ? $field : $this->query->qn($field);
            $field = $prefix ? $this->query->qn($prefix) . '.' . $field : $field;
            $this->query->select($field);
        }
    }

    /**
     * @return \JDatabaseQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Execute the query as a "select" statement.
     * @return Collection
     */
    public function get()
    {
        $query = $this->getQuery();
        $models = $this->container->db->queryObjectList($query);

        return new Collection($models);
    }

    /**
     * Add prefix to the where statement given
     * @param $sql
     */
    public function wherePrefix($sql)
    {
        $prefix = (isset($this->tablePrefix) && strlen($this->tablePrefix) > 0) ? $this->query->qn($this->tablePrefix) . '.' : '';
        $this->query->where($prefix . $sql);
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * @param string $tablePrefix
     */
    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;

        // Prefix the table if necessary
        $prefix = (strlen($this->tablePrefix) > 0) ? ' AS ' . $this->query->qn($this->tablePrefix) : '';

        // Autoperform FROM statement
        $this->query->clear('from');
        $this->query->from($this->query->qn($this->tableName) . $prefix);
    }
}