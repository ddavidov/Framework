<?php

namespace Zoolanders\Framework\Model\Item;

use Zoolanders\Framework\Model\Database\Access;

trait BasicFilters
{
    use Access;

    /**
     * Apply general filters like searchable, publicated, etc
     */
    protected function filterIds($ids)
    {
       return $this->filterIn('id', $ids);
    }

    protected function filterSearchable($state = 1)
    {
        $state = (int) $state;
        $this->wherePrefix('searchable = ' . $state);
        return $this;
    }

    protected function filterState($state = 1)
    {
        $state = (int) $state;
        $this->wherePrefix('state = ' . $state);
        return $this;
    }

    protected function filterCreators($ids)
    {
        return $this->filterIn('created_by', $ids);
    }

    protected function filterEditors($ids)
    {
        return $this->filterIn('modified_by', $ids);
    }

    /**
     * @param $ids
     * @return $this
     */
    protected function filterIn($field, $ids)
    {
        settype($ids, 'array');

        if (count($ids)) {
            $this->wherePrefix($this->query->qn($field) . ' IN (' . implode(', ', $this->query->q($ids)) . ')');
        }

        return $this;
    }

    protected function filterFrontpage()
    {
        $this->query->join('LEFT', ZOO_TABLE_CATEGORY_ITEM . " AS f ON {$this->query->qn($this->tablePrefix)}.id = f.item_id");
        $this->query->where('f.category_id  = 0');
    }


    protected function otherFilters() {
        // Created
        if ($date = $this->getState('created', array())) {
            $date = array_shift($date);

            $sql_value = "a.created";
            $value = $date->get('value', '');
            $value_from = !empty($value) ? $value : '';
            $value_to = $date->get('value_to', '');
            $search_type = $date->get('type', false);
            $period_mode = $date->get('period_mode', 'static');
            $interval = $date->get('interval', 0);
            $interval_unit = $date->get('interval_unit', '');
            $datetime = $date->get('datetime', false);

            $this->query->where($this->getDateSearch(compact('sql_value', 'value', 'value_from', 'value_to', 'search_type', 'period_mode', 'interval', 'interval_unit', 'datetime')));
        }

        // Modified
        if ($date = $this->getState('modified', array())) {
            $date = array_shift($date);

            $sql_value = "a.modified";
            $value = $date->get('value', '');
            $value_from = !empty($value) ? $value : '';
            $value_to = $date->get('value_to', '');
            $search_type = $date->get('type', false);
            $period_mode = $date->get('period_mode', 'static');
            $interval = $date->get('interval', 0);
            $interval_unit = $date->get('interval_unit', '');
            $datetime = $date->get('datetime', false);

            $this->query->where($this->getDateSearch(compact('sql_value', 'value', 'value_from', 'value_to', 'search_type', 'period_mode', 'interval', 'interval_unit', 'datetime')));
        }

        // Published up
        if ($date = $this->getState('published', array())) {
            $date = array_shift($date);

            $sql_value = "a.publish_up";
            $value = $date->get('value', '');
            $value_from = !empty($value) ? $value : '';
            $value_to = $date->get('value_to', '');
            $search_type = $date->get('type', false);
            $period_mode = $date->get('period_mode', 'static');
            $interval = $date->get('interval', 0);
            $interval_unit = $date->get('interval_unit', '');
            $datetime = $date->get('datetime', false);

            $this->query->where($this->getDateSearch(compact('sql_value', 'value', 'value_from', 'value_to', 'search_type', 'period_mode', 'interval', 'interval_unit', 'datetime')));

            // default
        } elseif (!$this->getState('created') && !$this->getState('modified')) {
            $where = array();
            $where[] = 'a.publish_up = ' . $null;
            $where[] = 'a.publish_up <= ' . $now;
            $this->query->where('(' . implode(' OR ', $where) . ')');
        }

        // Published down
        if ($date = $this->getState('published_down', array())) {
            $date = array_shift($date);

            $sql_value = "a.publish_down";
            $value = $date->get('value', '');
            $value_from = !empty($value) ? $value : '';
            $value_to = $date->get('value_to', '');
            $search_type = $date->get('type', false);
            $period_mode = $date->get('period_mode', 'static');
            $interval = $date->get('interval', 0);
            $interval_unit = $date->get('interval_unit', '');
            $datetime = $date->get('datetime', false);

            $this->query->where($this->getDateSearch(compact('sql_value', 'value', 'value_from', 'value_to', 'search_type', 'period_mode', 'interval', 'interval_unit', 'datetime')));

            // default
        } else if (!$this->getState('published_down')) {
            $where = array();
            $where[] = 'a.publish_down = ' . $null;
            $where[] = 'a.publish_down >= ' . $now;
            $this->query->where('(' . implode(' OR ', $where) . ')');
        }
        
    }

    /**
     * Get only the published items
     */
    protected function filterPublished()
    {
        // of course state has to be 1
        $this->state(1);

        $null = $this->container->db->getNullDate();
        $now = new \JDate();

        // within the published dates
        $where = [
            $this->query->qn('a.publish_down') . ' = ' . $this->query->q($null),
            $this->query->qn('a.publish_down'). ' >= ' . $this->query->q($now)
        ];

        $this->query->where('( '. implode(" OR ", $where) . ')');

        $where = [
            $this->query->qn('a.publish_up') . ' = ' . $this->query->q($null),
            $this->query->qn('a.publish_up'). ' <= ' . $this->query->q($now)
        ];

        $this->query->where('( '. implode(" OR ", $where) . ')');
    }
}