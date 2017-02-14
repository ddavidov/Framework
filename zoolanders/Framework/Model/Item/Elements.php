<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Model\Item;

trait Elements
{
    protected $elementJoins = 0;

    abstract public function join($table, $condition, $alias, $type = 'LEFT');

    abstract public function where($field, $operator, $value);

    abstract public function orWhere($field, $operator, $value);

    abstract public function whereAny($field, $operator, $value);

    abstract public function orWhereAny($field, $operator, $value);

    abstract public function whereRaw($sql);

    abstract public function orWhereRaw($sql);

    abstract public function getQuery();

    abstract public function getTablePrefix();

    /**
     * @param $id
     * @param $operator
     * @param $value
     */
    public function whereElement($id, $operator, $value, $convert = false)
    {
        // Normal search
        $value = $this->getQuery()->q($value);
        $alias = $this->getJoinElementAlias();

        $this->joinElement($id);

        // Decimal conversion fix
        if (strtoupper($convert) == 'DECIMAL') {
            $convert = 'DECIMAL(10,2)';
        }

        // validate value to be sure it is number
        if (strtoupper($convert) == 'SIGNED') {
            $value = floatval($value);
        }

        $this->where( $this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));

        if ($convert) {
            $this->where( "CONVERT(TRIM( " . $this->getQuery()->qn($alias . '.value') . "+0), {$convert})", $operator, $value);
            return;
        }

        $this->where( "TRIM( " . $this->getQuery()->qn($alias . '.value') . " )", $operator, $value);
    }

    /**
     * @param $id
     * @param $operator
     * @param $value
     */
    public function whereAnyElement($id, $operator, $value)
    {
        // Normal search
        $value = $this->getQuery()->q($value);
        $alias = $this->getJoinElementAlias();

        $this->joinElement($id);

        $this->where( $this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));
        $this->whereAny( "TRIM( " . $this->getQuery()->qn($alias . '.value') . " )", $operator, $value);
    }

    /**
     * @param $id
     * @param $operator
     * @param $value
     */
    public function orWhereAnyElement($id, $operator, $value)
    {
        // Normal search
        $value = $this->getQuery()->q($value);
        $alias = $this->getJoinElementAlias();

        $this->joinElement($id);

        $this->where( $this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));
        $this->orWhereAny( "TRIM( " . $this->getQuery()->qn($alias . '.value') . " )", $operator, $value);
    }

    /**
     * @param $id
     * @param $operator
     * @param $value
     */
    public function orWhereElement($id, $operator, $value)
    {
        // Normal search
        $value = $this->getQuery()->q($value);
        $alias = $this->getJoinElementAlias();

        $this->joinElement($id);

        $this->orWhere( $this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));
        $this->orWhere("TRIM( " . $this->getQuery()->qn($alias . '.value') . " )", $operator, $value);
    }

    /**
     * @param $id
     * @param $values
     */
    public function whereElementHasAll($id, $values)
    {
        settype($values, 'array');

        $alias = $this->getJoinElementAlias();
        $this->joinElement($id);

        $this->where( $this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));

        // Normal selects / radio / etc (ElementOption)
        $multiples = $this->getMultipleElementValues($values, $alias);

        $this->whereRaw( '(' . implode(" AND ", $multiples));
    }

    /**
     * @param $id
     * @param $values
     */
    public function whereElementHasAny($id, $values)
    {
        settype($values, 'array');

        $alias = $this->getJoinElementAlias();
        $this->joinElement($id);

        $this->where( $this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));
        $multiples = $this->getMultipleElementValues($values, $alias);


        $this->whereRaw( '(' . implode(" OR ", $multiples));
    }

    /**
     * @param $id
     */
    public function joinElement($id)
    {
        // new join here
        $this->elementJoins++;

        $alias = $this->getJoinElementAlias();

        $this->join(
            ZOO_TABLE_SEARCH,
            $this->getQuery()->qn($this->getTablePrefix() . ".id") . ' = ' . $this->getQuery()->qn($alias . '.item_id') .
            ' AND ' . $this->getQuery()->qn($alias . '.element_id') . ' = ' . $this->getQuery()->q($id),
            $alias
        );
    }

    /**
     * @return string
     */
    protected function getJoinElementAlias()
    {
        return 'b' . $this->elementJoins;
    }

    /**
     * @param $values
     * @param $alias
     * @return array
     */
    protected function getMultipleElementValues($values, $alias)
    {
        $multiples = [];

        // Normal selects / radio / etc (ElementOption)
        foreach ($values as $value) {
            $multiple = "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim($this->getQuery()->escape($value))) . " OR ";
            $multiple .= "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim($this->getQuery()->escape($value) . "\n%")) . " OR ";
            $multiple .= "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim($this->getQuery()->escape($value) . ' %')) . " OR ";
            $multiple .= "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim("%\n" . $this->getQuery()->escape($value))) . " OR ";
            $multiple .= "TRIM(" . $this->getQuery()->qn($alias . '.value') . ") LIKE " . $this->getQuery()->q(trim("%\n" . $this->getQuery()->escape($value) . "\n%"));
            $multiples[] = "(" . $multiple . ")";
        }

        return $multiples;
    }
}