<?php

namespace Zoolanders\Framework\Model\Item\Element;

trait Date
{
    /**
     * date format yyyy-mm-dd
     * @var string
     */
    protected static $dateRegexp = '/^(19[0-9]{2}|2[0-9]{3})-(0[1-9]|1[012])-([123]0|[012][1-9]|31)/';
    
    /**
     * @param $field
     * @param $value
     *
     * @return Database
     */
    protected function filterElementDate($field, $value)
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

        $this->whereRaw("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     *
     * @return Database
     */
    protected function filterElementDateTime($field, $value)
    {
        list($date, $from, $to) = $this->prepareDateValue($value);

        // Super complicated string stuff for dates.
        // We'll someday move this to a dedicated date table
        $wheres = [
            "({$field} LIKE '%{$date}%')",
            "('{$date}' BETWEEN {$field} AND {$field}) AND {$field} NOT REGEXP '[[.LF.]]')"
        ];

        $this->whereRaw("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function filterElementDateFrom($field, $value)
    {
        list($date, $from, $to) = $this->prepareDateValue($value);

        $this->getQuery()->where("( (SUBSTR({$field}, -19) >= {$from}) OR ({$from} <= SUBSTR({$field}, -19)) )");

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function filterElementDateTo($field, $value)
    {
        list($date, $from, $to) = $this->prepareDateValue($value);

        $this->getQuery()->where("( (SUBSTR({$field}, 1, 19) <= {$to}) OR ({$to} >= SUBSTR({$field}, 1, 19)) )");

        return $this;
    }

    /**
     * @param $field
     * @param $valueFrom
     * @param $valueTo
     * @return $this
     */
    protected function filterElementDateBetween($field, $valueFrom, $valueTo)
    {
        list($dateFrom, $from, $toFrom) = $this->prepareDateTimeValue($valueFrom);
        list($dateTo, $fromTo, $to) = $this->prepareDateTimeValue($valueTo);

        // Super complicated string stuff for dates.
        // We'll someday move this to a dedicated date table
        $wheres = [
            "({$from} BETWEEN SUBSTR({$field}, 1, 19) AND SUBSTR({$field}, -19))",
            "({$to} BETWEEN SUBSTR({$field}, 1, 19) AND SUBSTR({$field}, -19))",
            "(SUBSTR({$field}, 1, 19) BETWEEN $from AND $to)",
            "(SUBSTR({$field}, -19) BETWEEN {$from} AND {$to})"
        ];

        $this->whereRaw("(" . implode(" OR ", $wheres) . ")");

        return $this;
    }
}