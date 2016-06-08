<?php

namespace Zoolanders\Framework\Model\Item;

trait Elements
{
    protected $elementJoins = 0;

    abstract public function join($table, $condition, $alias, $type = 'LEFT');

    abstract public function where($field, $operator, $value);

    abstract public function orWhere($field, $operator, $value);

    abstract public function whereRaw($sql);

    abstract public function orWhereRaw($sql);

    abstract public function getQuery();

    abstract public function getTablePrefix();

    /**
     * @param $id
     * @param $operator
     * @param $value
     */
    public function whereElement($id, $operator, $value)
    {
        // Normal search
        $value = $this->getQuery()->q($value);
        $alias = $this->getJoinElementAlias();

        $this->joinElement($id);

        $this->where( $this->getQuery()->qn($alias . '.element_id'), '=', $this->getQuery()->q($id));
        $this->where( "TRIM( " . $this->getQuery()->qn($alias . '.value') . " )", $operator, $value);
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
     * Apply element filters
     */
    protected function elementFilters(&$query)
    {
        $wheres = array('AND' => array(), 'OR' => array());

        // Elements filtering
        $k = 0;

        // get the filter query
        $nestedFilter = $this->getNestedArrayFilter();
        $i = 0;
        $nestedFilterQuery = '';
        $join_info = array();
        foreach ($nestedFilter as $app => &$types) {

            // iterate over types
            $types_queries = array();
            foreach ($types as $type => &$type_elements) {

                // init vars
                $elements_where = array('AND' => array(), 'OR' => array());

                // set the type query
                $type_query = 'a.type LIKE ' . $this->_db->Quote($type);

                // get individual element query
                foreach ($type_elements as $element) {
                    $this->getElementSearch($element, $k, $elements_where, $join_info);
                }

                // merge elements ORs / ANDs
                $elements_query = '';
                if (count($elements_where['OR'])) {
                    $type_query .= ' AND (' . implode(' OR ', $elements_where['OR']) . ')';
                }

                if (count($elements_where['AND'])) {
                    $type_query .= ' AND (' . implode(' AND ', $elements_where['AND']) . ')';
                }

                // save type query
                $types_queries[] = $type_query;
            }

            // types query
            $types_query = count($types_queries) ? implode(' OR ', $types_queries) : '';

            // app query
            $app_query = in_array($app, $this->apps) ? 'a.application_id = ' . (int)$app : '';

            // get the app->type->elements query
            $logic = $i == 0 ? '' : 'OR '; // must be AND only of first iterance, then must be OR
            if ($app_query && $types_query) {
                $nestedFilterQuery .= $logic . '(' . $app_query . ' AND (' . $types_query . '))';
            } else if ($app_query || $types_query) {
                $nestedFilterQuery .= $logic . '(' . $app_query . $types_query . ')';
            }

            $i++;
        }

        // add nestedFilterQuery
        if (!empty($nestedFilterQuery)) $wheres['AND'][] = '(' . $nestedFilterQuery . ')';

        // At the end, merge ORs
        if (count($wheres['OR'])) {
            $query->where('(' . implode(' OR ', $wheres['OR']) . ')');
        }

        // and the ANDs
        foreach ($wheres['AND'] as $where) {
            $query->where($where);
        }

        // Add repeatable joins
        $this->addRepeatableJoins($query, $k, $join_info);
    }

    /**
     * @return string
     */
    protected function getJoinElementAlias()
    {
        return 'b' . $this->elementJoins;
    }
}