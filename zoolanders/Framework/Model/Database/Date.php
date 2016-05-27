<?php

namespace Zoolanders\Framework\Model\Database;

use Zoolanders\Framework\Model\Database;

trait Date
{
    /**
     * date format yyyy-mm-dd
     * @var string
     */
    protected static $dateRegexp = '/^(19[0-9]{2}|2[0-9]{3})-(0[1-9]|1[012])-([123]0|[012][1-9]|31)/';

    protected static $validIntervals = array('MONTH', 'DAY', 'WEEK', 'YEAR', 'MINUTE', 'SECOND', 'HOUR');

    /**
     * Replace placeholders (if string and present) [yesterday] , [today] , [tomorrow] in a date
     * @param mixed $value
     * @return mixed
     */
    public function replaceDatePlaceholders($value)
    {
        // init vars
        $tzoffset = $this->container->date->getOffset();

        if (is_string($value)) {
            // replace vars
            $yesterday = $this->container->date->create('yesterday', $tzoffset);
            $today = $this->container->date->create('today', $tzoffset);
            $tomorrow = $this->container->date->create('tomorrow', $tzoffset);

            $yesterday = substr($yesterday, 0, 10) . ' 23:59:59';
            $today = substr($today, 0, 10) . ' 23:59:59';
            $tomorrow = substr($tomorrow, 0, 10) . ' 23:59:59';

            $value = preg_replace(
                array('/\[yesterday\]/', '/\[today\]/', '/\[tomorrow\]/'),
                array($yesterday, $today, $tomorrow),
                $value
            );
        }

        return $value;
    }

    /**
     * @param \JDatabaseQuery $query
     * @param $field
     * @param $value
     *
     * @return Database
     */
    protected function filterDate($field, $value)
    {
        list($date, $from, $to) = $this->prepareDateValue($value);

        // Super complicated string stuff for dates.
        // We'll someday move this to a dedicated date table
        $wheres = [
            "({$field} LIKE '%{$date}%')",
            "(SUBSTR({$field}, 1, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 1, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 1, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 21, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 41, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 41, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 61, 19) BETWEEN {$from} AND {$to})",
            "(SUBSTR({$field}, 81, 19) BETWEEN {$from} AND {$to})"
        ];

        $this-getQuery()->where("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param \JDatabaseQuery $query
     * @param $field
     * @param $value
     *
     * @return Database
     */
    protected function filterDateTime($field, $value)
    {
        list($date, $from, $to) = $this->prepareDateValue($value);

        // Super complicated string stuff for dates.
        // We'll someday move this to a dedicated date table
        $wheres = [
            "({$field} LIKE '%{$date}%')",
            "('{$date}' BETWEEN {$field} AND {$field}) AND {$field} NOT REGEXP '[[.LF.]]')"
        ];

        $this->getQuery()->where("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    protected function filterDateTimeFrom($field, $value)
    {
        list($date, $from, $to) = $this->prepareDateValue($value);

        $this->getQuery()->where("( ({$field} >= {$from}) OR ({$from} <= {$field}) )");

        return $this;
    }

    protected function filterDateFrom($field, $value)
    {
        list($date, $from, $to) = $this->prepareDateValue($value);

        $this->getQuery()->where("( (SUBSTR({$field}, -19) >= {$from}) OR ({$from} <= SUBSTR({$field}, -19)) )");

        return $this;
    }

    protected function filterDateTo($field, $value)
    {
        list($date, $from, $to) = $this->prepareDateValue($value);

        $this->getQuery()->where("( (SUBSTR({$field}, 1, 19) <= {$to}) OR ({$to} >= SUBSTR({$field}, 1, 19)) )");

        return $this;
    }

    protected function filterDateBetween($field, $valueFrom, $valueTo)
    {
        list($dateFrom, $from, $toFrom) = $this->prepareDateValue($valueFrom);
        list($dateTo, $fromTo, $to) = $this->prepareDateValue($valueTo);

        // Super complicated string stuff for dates.
        // We'll someday move this to a dedicated date table
        $wheres = [
            "({$from} BETWEEN SUBSTR({$field}, 1, 19) AND SUBSTR({$field}, -19))",
            "({$to} BETWEEN SUBSTR({$field}, 1, 19) AND SUBSTR({$field}, -19))",
            "(SUBSTR({$field}, 1, 19) BETWEEN $from AND $to)",
            "(SUBSTR({$field}, -19) BETWEEN {$from} AND {$to})"
        ];

        $this->getQuery()->where("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    protected function filterDateTimeBetween($field, $valueFrom, $valueTo)
    {
        list($dateFrom, $from, $toFrom) = $this->prepareDateValue($valueFrom);
        list($dateTo, $fromTo, $to) = $this->prepareDateValue($valueTo);

        // Super complicated string stuff for dates.
        // We'll someday move this to a dedicated date table
        $wheres = [
            "({$from} BETWEEN {$field} AND {$field})",
            "({$to} BETWEEN {$field} AND {$field})",
            "({$field} BETWEEN {$from} AND {$to})"
        ];

        $this->getQuery()->where("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param $query \JDatabaseQuery
     * @param $field string
     * @param $interval int
     * @param $unit string
     * @return $this
     */
    protected function filterDateWithinInterval($field, $interval, $unit)
    {
        $unit = strtoupper($unit);
        $interval = (int) $interval;

        if (!in_array($unit, self::$validIntervals)) {
            $unit = 'WEEK';
        }

        if ($interval > 0) {
            $this->getQuery()->where("( {$field} BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL {$interval} {$unit}))");
            return $this;
        }

        $this->getQuery()->where("( {$field} BETWEEN DATE_ADD(NOW(), INTERVAL {$interval} {$unit}) AND NOW())");
        return $this;

    }

    /**
     * @param $query
     * @param $value
     * @return array
     */
    protected function prepareDateValue($value)
    {
        // init vars
        $tzoffset = $this->container->date->getOffset();

        // replace placeholders
        $value = $this->replaceDatePlaceholders($value);

        // remove date time stuff
        $date = substr($value, 0, 10);
        $from = $date . ' 00:00:00';
        $to = $date . ' 23:59:59';

        // set offset if valid date format
        $from = preg_match(self::$dateRegexp, $from) ? $this->container->date->create($from, $tzoffset)->toSQL() : $from;
        $to = preg_match(self::$dateRegexp, $to) ? $this->container->date->create($to, $tzoffset)->toSQL() : $to;

        // set quotes
        $from = $this->getQuery()->q($this->getQuery()->escape($from));
        $to = $this->getQuery()->q($this->getQuery()->escape($to));

        // set offset and escape quotes
        $date = preg_match(self::$dateRegexp, $date) ? $this->container->date->create($date, $tzoffset)->toSQL() : $date;
        $date = $this->getQuery()->q($this->getQuery()->escape($date));

        return array($date, $from, $to);
    }
}