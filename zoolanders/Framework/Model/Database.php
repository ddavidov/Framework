<?php

namespace Zoolanders\Framework\Model;

use Illuminate\Support\Collection;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Model\Database\Date;
use Zoolanders\Framework\Utils\IsString;

defined('_JEXEC') or die;

abstract class Database extends Model
{
    use Date, IsString;

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

    /**
     * List of where clauses to be build into an AND clause
     * @var array
     */
    protected $wheres = [];

    /**
     * List of where clauses to build into an OR clause
     * @var array
     */
    protected $orWheres = [];

    /**
     * List of tables to join (grouped by join type) with their desired aliases, if needed
     * @var array
     */
    protected $joins = [

    ];

    /**
     * Database constructor.
     * @param Container $container
     */
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
     * @return \JDatabaseQuery
     */
    public function buildQuery()
    {
        $query = $this->getQuery();

        // Join stuff
        if (count($this->joins)) {
            foreach ($this->joins as $type => $tables) {
                foreach ($tables as $sql) {
                    $query->join($type, $sql);
                }
            }
        }

        // At the end, merge ORs
        if (count($this->orWheres)) {
            $query->where('(' . implode(' OR ', $this->orWheres) . ')');
        }

        // and the ANDs
        foreach ($this->wheres as $where) {
            $query->where($where);
        }

        return $query;
    }

    /**
     * Execute the query as a "select" statement.
     * @return Collection
     */
    public function get()
    {
        $query = $this->buildQuery();
        $models = $this->container->db->queryObjectList($query);

        return new Collection($models);
    }

    /**
     * @param $fieldOrCallable
     * @param $operator
     * @param $value
     * @return $this
     */
    public function where($fieldOrCallable, $operator, $value)
    {
        if (is_callable($fieldOrCallable)) {
            call_user_func_array($fieldOrCallable, [&$this->query]);
            return $this;
        }

        $this->setupOperatorAndValue($operator, $value);

        $this->wheres[] = $this->query->qn($fieldOrCallable) . " " . $operator . " " . $value;
        return $this;
    }

    /**
     * @param $fieldOrCallable
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orWhere($fieldOrCallable, $operator, $value)
    {
        if (is_callable($fieldOrCallable)) {
            call_user_func_array($fieldOrCallable, [&$this->query]);
            return $this;
        }

        $this->orWheres[] = $this->query->qn($fieldOrCallable) . " " . $operator . " " . $this->query->q($value);
        return $this;
    }

    /**
     * @param $field
     * @param $from
     * @param $to
     * @return $this
     */
    public function whereBetween($field, $from, $to)
    {
        $this->wheres[] = $this->query->qn($field) . " BETWEEN " . $this->query->q($from) . " AND " . $this->query->q($to);
        return $this;
    }

    /**
     * @param $field
     * @param $from
     * @param $to
     * @return $this
     */
    public function orWhereBetween($field, $from, $to)
    {
        $this->orWheres[] = $this->query->qn($field) . " BETWEEN " . $this->query->q($from) . " AND " . $this->query->q($to);
        return $this;
    }

    /**
     * @param $field
     * @param $operator
     * @param $value
     * @return $this
     */
    public function whereAny($field, $operator, $value)
    {
        if ($this->isString($value)) {
            $value = explode(" ", $value);
        }

        settype($value, 'array');

        $wheres = [];
        foreach ($value as $v) {
            $this->setupOperatorAndValue($operator, $v);
            $wheres[] = $this->query->qn($field) . " " . $operator . " " . $v;
        }

        $this->wheres[] = '(' . implode(" OR ", $wheres) . ')';

        return $this;
    }

    /**
     * @param $field
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orWhereAny($field, $operator, $value)
    {
        if ($this->isString($value)) {
            $value = explode(" ", $value);
        }

        settype($value, 'array');

        $wheres = [];
        foreach ($value as $v) {
            $this->setupOperatorAndValue($operator, $v);
            $wheres[] = $this->query->qn($field) . " " . $operator . " " . $v;
        }

        $this->orWheres[] = '(' . implode(" OR ", $wheres) . ')';

        return $this;
    }

    /**
     * @param $sql
     * @return $this
     */
    public function whereRaw($sql)
    {
        $this->wheres[] = $sql;
        return $this;
    }

    /**
     * @param $sql
     * @return $this
     */
    public function orWhereRaw($sql)
    {
        $this->orWheres[] = $sql;
        return $this;
    }

    /**
     * Add prefix to the where statement given
     * @param $sql
     */
    public function wherePrefix($sql)
    {
        $this->wheres[] = $this->getPrefix() . $sql;
    }

    /**
     * @param $sql
     */
    public function orWherePrefix($sql)
    {
        $this->orWheres[] = $this->getPrefix() . $sql;
    }

    /**
     * Join a table with a defined alias
     * @param $table
     * @param bool $alias
     * @param $type
     */
    public function join($table, $condition, $alias = false, $type = 'LEFT')
    {
        $type = strtoupper($type);

        if (!$type) {
            $type = 'LEFT';
        }

        if (!isset($this->joins[$type])) {
            $this->joins[$type] = [];
        }

        $query = $this->getQuery();
        $table = $query->qn($table);

        if ($alias) {
            $table .= ' AS ' . $query->qn($alias);
        }

        $this->joins[$type][] = $table . ' ON ' . $condition;
    }

    /**
     * @param $ids
     * @return $this
     */
    public function filterIn($field, $ids)
    {
        settype($ids, 'array');

        if (count($ids)) {
            $this->wherePrefix($this->query->qn($field) . ' IN (' . implode(', ', $this->query->q($ids)) . ')');
        }

        return $this;
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

    /**
     * @return string
     */
    protected function getPrefix()
    {
        $prefix = (isset($this->tablePrefix) && strlen($this->tablePrefix) > 0) ? $this->query->qn($this->tablePrefix) . '.' : '';
        return $prefix;
    }

    /**
     * @param $operator
     * @param $value
     */
    protected function setupOperatorAndValue(&$operator, &$value)
    {
        $value = $this->query->q($value);

        switch (strtolower($operator))
        {
            case 'in':
                settype($value, 'array');
                $value = '(' . implode(",", $value) . ')';
                break;
        }
    }
}
