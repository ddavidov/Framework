<?php
/**
 * @package     ZOOlanders
 * @version     3.3.16
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Model;

use Zoolanders\Framework\Model\Database\Date;
use Zoolanders\Framework\Model\Item\Basics;
use Zoolanders\Framework\Model\Item\Categories;
use Zoolanders\Framework\Model\Item\Tags;

defined('_JEXEC') or die();

class Item extends Database
{
    use Basics, Tags, Categories;

    protected $tablePrefix = 'a';
    protected $tableName = ZOO_TABLE_ITEM;
    protected $join_cats = false;
    protected $join_frontpage = false;
    protected $join_tags = false;

    /*
        Function: _buildQueryFrom
            Builds FROM tables list for the query
    */
    protected function _buildQueryFrom(&$query)
    {
        $query->from(ZOO_TABLE_ITEM . ' AS a');
    }

    /*
        Function: _buildQueryJoins
            Builds JOINS clauses for the query
    */
    protected function _buildQueryJoins(&$query)
    {
        // frontpage
        if ($this->join_frontpage) {
            $query->join('LEFT', ZOO_TABLE_CATEGORY_ITEM . " AS f ON a.id = f.item_id");
        }

        // tags
        if ($this->join_tags) {
            $query->join('LEFT', ZOO_TABLE_TAG . " AS t ON a.id = t.item_id");
        }

        // elements
        if ($orderby = $this->getState('order_by')) {
            // get item ordering
            list($join, $order) = $this->_getItemOrder($orderby);

            // save order for order query
            $this->orderby = $order;

            // join
            if ($join) { // don't use escape() here
                $query->leftJoin($join);
            }
        }
    }

    /*
        Function: _buildQueryWhere
            Builds WHERE query
    */
    protected function _buildQueryWhere(&$query)
    {
        // Apply basic filters
        $this->basicFilters($query);

        // Element filters
        $this->elementFilters($query);
    }

    /*
        Function: _buildQueryGroup
            Builds a GROUP BY clause for the query
    */
    protected function _buildQueryGroup(&$query)
    {
        if ($group_by = $this->_db->escape($this->getState('group_by'))) {
            $query->group('a.' . $group_by);
        }
    }

    /*
        Function: _buildQueryOrder
            Bilds ORDER BY query
    */
    protected function _buildQueryOrder(&$query)
    {
        // custom order
        if ($this->getState('order_by') && isset($this->orderby)) {
            $query->order($this->orderby);
        }
    }

    /**
     * Create and returns a nested array of App->Type->Elements
     */
    protected function getNestedArrayFilter()
    {
        // init vars
        $this->apps = $this->getState('application', array());
        $this->types = $this->getState('type', array());
        $elements = $this->getState('element', array());

        // if no filter data, abort
        if (empty($this->apps) && empty($this->types) && empty($elements)) {
            return array();
        }

        // convert apps into raw array
        if (count($this->apps)) foreach ($this->apps as $key => $app) {
            $this->apps[$key] = $app->get('value', '');
        }

        // convert types into raw array
        if (count($this->types)) foreach ($this->types as $key => $type) {
            $this->types[$key] = $type->get('value', '');
        }

        // get apps selected objects, or all if none filtered
        $apps = $this->app->table->application->all(array('conditions' => count($this->apps) ? 'id IN(' . implode(',', $this->apps) . ')' : ''));

        // create a nested array with all app/type/elements filtering data
        $filters = array();
        foreach ($apps as $app) {

            $filters[$app->id] = array();
            foreach ($app->getTypes() as $type) {

                // get type elements
                $type_elements = $type->getElements();
                $type_elements = array_keys($type_elements);

                // get selected elements
                $elements = $this->getState('element', array());

                // filter the current type elements
                $valid_elements = array();
                if ($elements) foreach ($elements as $key => $element) {
                    $identifier = $element->get('id');

                    // if element part of current type, it's valid
                    if (in_array($identifier, $type_elements)) {
                        $valid_elements[] = $element;

                        // remove current element to avoid revalidation
                        unset($elements[$key]);
                    }
                }

                // if there are elements for current type, or type is selected for filtering
                if (count($valid_elements) || in_array($type->id, $this->types)) {

                    // save the type and it's elements
                    $filters[$app->id][$type->id] = $valid_elements;
                }
            }
        }

        return $filters;
    }
    
    /**
     * Get the multiple values search sql
     */
    protected function getElementMultipleSearch($identifier, $values, $mode, $k, $is_select = true)
    {
        $el_where = "b$k.element_id = " . $this->_db->Quote($identifier);

        // lets be sure mode is set
        $mode = $mode ? $mode : "AND";

        $multiples = array();

        // Normal selects / radio / etc (ElementOption)
        if ($is_select) {
            foreach ($values as $value) {
                $multiple = "TRIM(b$k.value) LIKE " . $this->_db->Quote(trim($this->_db->escape($value))) . " OR ";
                $multiple .= "TRIM(b$k.value) LIKE " . $this->_db->Quote(trim($this->_db->escape($value) . "\n%")) . " OR ";
                $multiple .= "TRIM(b$k.value) LIKE " . $this->_db->Quote(trim("%\n" . $this->_db->escape($value))) . " OR ";
                $multiple .= "TRIM(b$k.value) LIKE " . $this->_db->Quote(trim("%\n" . $this->_db->escape($value) . "\n%"));
                $multiples[] = "(" . $multiple . ")";
            }
        } // This covers country element too
        else {
            foreach ($values as $value) {
                $multiple = "TRIM(b$k.value) LIKE " . $this->_db->Quote(trim($this->_db->escape($value))) . " OR ";
                $multiple .= "TRIM(b$k.value) LIKE " . $this->_db->Quote(trim($this->_db->escape($value) . ' %')) . " OR ";
                $multiple .= "TRIM(b$k.value) LIKE " . $this->_db->Quote(trim('% ' . $this->_db->escape($value))) . " OR ";
                $multiple .= "TRIM(b$k.value) LIKE " . $this->_db->Quote(trim('% ' . $this->_db->escape($value) . ' %'));
                $multiples[] = "(" . $multiple . ")";
            }
        }

        $el_where .= " AND (" . implode(" " . $mode . " ", $multiples) . ")";

        return $el_where;
    }

    /**
     * _getItemOrder - Returns ORDER query from an array of item order options
     *
     * @param array $order Array of order params
     * Example:array(0 => '_itemcreated', 1 => '_reversed', 2 => '_random')
     */
    protected function _getItemOrder($order)
    {
        // if string, try to convert ordering
        if (is_string($order)) {
            $order = $this->app->itemorder->convert($order);
        }

        $result = array(null, null);
        $order = (array)$order;

        // remove empty and duplicate values
        $order = array_unique(array_filter($order));

        // if random return immediately
        if (in_array('_random', $order)) {
            $result[1] = 'RAND()';
            return $result;
        }

        // get order dir
        if (($index = array_search('_reversed', $order)) !== false) {
            $reversed = 'DESC';
            unset($order[$index]);
        } else {
            $reversed = 'ASC';
        }

        // get ordering type
        $alphanumeric = false;
        if (($index = array_search('_alphanumeric', $order)) !== false) {
            $alphanumeric = true;
            unset($order[$index]);
        }

        // save item priority state
        $priority = false;
        if (($index = array_search('_priority', $order)) !== false) {
            $priority = true;
            unset($order[$index]);
        }

        // set default ordering attribute
        if (empty($order)) {
            $order[] = '_itemname';
        }

        // if there is a none core element present, ordering will only take place for those elements
        if (count($order) > 1) {
            $order = array_filter($order, create_function('$a', 'return strpos($a, "_item") === false;'));
        }

        // order by core attribute
        foreach ($order as $element) {
            if (strpos($element, '_item') === 0) {
                $var = str_replace('_item', '', $element);
                if ($alphanumeric) {
                    $result[1] = $reversed == 'ASC' ? "a.$var+0<>0 DESC, a.$var+0, a.$var" : "a.$var+0<>0, a.$var+0 DESC, a.$var DESC";
                } else {
                    $result[1] = $reversed == 'ASC' ? "a.$var" : "a.$var DESC";
                }
            }
        }

        // else order by elements
        if (!isset($result[1])) {
            $result[0] = ZOO_TABLE_SEARCH . " AS s ON a.id = s.item_id AND s.element_id IN ('" . implode("', '", $order) . "')";
            if ($alphanumeric) {
                $result[1] = $reversed == 'ASC' ? "ISNULL(s.value), s.value+0<>0 DESC, s.value+0, s.value" : "ISNULL(s.value), s.value+0<>0, s.value+0 DESC, s.value DESC";
            } else {
                $result[1] = $reversed == 'ASC' ? "s.value" : "s.value DESC";
            }
        }

        // set priority at the end
        if ($priority) $result[1] = "a.priority $reversed, " . $result[1];

        return $result;
    }
}