<?php

namespace Zoolanders\Framework\Table;

class Item extends \ItemTable
{
    public function __construct($app)
    {
        parent::__construct($app);

        $this->class = '\Item';
    }

    /**
     * @param $application_id
     * @param $category_id
     * @param bool $published
     * @param null $user
     * @param string $orderby
     * @param int $offset
     * @param int $limit
     * @param bool $ignore_order_priority
     * @return array
     */
    public function getByCategory($application_id, $category_id, $published = false, $user = null, $orderby = "", $offset = 0, $limit = 0, $ignore_order_priority = false)
    {
        // get database
        $db = $this->database;

        // get dates
        $date = $this->app->date->create();
        $now = $db->Quote($date->toSQL());
        $null = $db->Quote($db->getNullDate());
        $language = \JFactory::getLanguage()->getTag();

        // get item ordering
        list($join, $order) = $this->_getItemOrder($orderby, $ignore_order_priority);

        $query = "SELECT a.*"
            . " FROM " . $this->name . " AS a"
            . " LEFT JOIN " . ZOO_TABLE_CATEGORY_ITEM . " AS b ON a.id = b.item_id"
            . " LEFT JOIN #__zoo_zl_item_languages AS l ON a.id = l.item_id"
            . ($join ? $join : "")
            . " WHERE a.application_id = " . (int)$application_id
            . " AND a." . $this->app->user->getDBAccessString($user)
            . ($published == true ? " AND a.state = 1"
                . " AND (l.language LIKE " . $db->q($language) . " AND l.enabled = 1)"
                . " AND (a.publish_up = " . $null . " OR a.publish_up <= " . $now . ")"
                . " AND (a.publish_down = " . $null . " OR a.publish_down >= " . $now . ")" : "")
            . " AND b.category_id " . (is_array($category_id) ? " IN (" . implode(",", $category_id) . ")" : " = " . (int)$category_id)
            . " GROUP BY a.id"
            . ($order ? " ORDER BY " . $order : "")
            . ($limit ? " LIMIT " . (int)$offset . "," . (int)$limit : "");

        $data = $this->_queryObjectList($query);

        return $data;
    }

    protected function _queryObjectList($query) {

        // query database
        $result = $this->database->query($query);

        // fetch objects and execute init callback
        $objects = array();
        while ($object = $this->database->fetchObject($result, $this->class)) {
            $objects[$object->{$this->key}] = $this->_initObject($object);
        }

        $this->database->freeResult($result);
        return $objects;
    }
}