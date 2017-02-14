<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */
/**
 * Ported from original ZOO class
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * Reworked by ZOOlanders
 */

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Utils\IsString;

class Date extends Service
{

    use IsString;

    /**
     * Create a JDate object
     *
     * @param mixed $time optional time to set the date object to (default: 'now'). @see JDate
     * @param mixed $offset The timezone offset to apply (default: null)
     *
     * @return \JDate The JDate object set to the given time / offset
     */
    public function create($time = 'now', $offset = null)
    {
        return \JFactory::getDate($time, $offset);
    }

    /**
     * Check if the given date is equal to today (day + month + year)
     *
     * @param \JDate $date the date to check
     *
     * @return boolean If the date is today
     */
    public function isToday($date)
    {
        // get dates
        $now = $this->create();
        $date = $this->create($date);

        return date('Y-m-d', $date->toUnix()) == date('Y-m-d', $now->toUnix());
    }

    /**
     * Check if the given date is equal to yesterday (day + month + year)
     *
     * @param \JDate $date the date to check
     *
     * @return boolean If the date is yesterday
     */
    public function isYesterday($date)
    {
        // get dates
        $now = $this->create();
        $date = $this->create($date);

        return date('Y-m-d', $date->toUnix()) == date('Y-m-d', $now->toUnix() - 86400);
    }

    /**
     * Get the time difference or the weekday difference (Date Format LC3) between today and the given date
     *
     * This method returns something like "20hr 10 min ago" if the date is today
     * Otherwise it returns something like "January 31st, 2012"
     *
     * @param \JDate $date The date to check
     *
     * @return string The text representing the time difference
     */
    public function getDeltaOrWeekdayText($date)
    {
        // get dates
        $now = $this->create();
        $date = $this->create($date);
        $delta = $now->toUnix() - $date->toUnix();

        if ($this->isToday($date)) {
            $hours = intval($delta / 3600);
            $hours = $hours > 0 ? $hours . \JText::_('hr') : '';
            $mins = intval(($delta % 3600) / 60);
            $mins = $mins > 0 ? ' ' . $mins . \JText::_('min') : '';
            $delta = $hours . $mins ? \JText::sprintf('%s ago', $hours . $mins) : \JText::_('1min ago');
        } else {
            $delta = \JHtml::_('date', $date->toSql(true), \JText::_('DATE_FORMAT_LC3') . ' H:i');
        }

        return $delta;
    }

    /**
     * Converts the format given to a version-independent format
     *
     * @param string $format The format to translate
     *
     * @return string The format in J1.5 version
     */
    public function format($format)
    {
        return $this->strftimeToDateFormat($format);
    }

    /**
     * Converts a date format to a format usable by strftime()
     *
     * @param string $dateFormat The dateformat in the joomla format
     *
     * @return string The date format in strftime() usable format
     */
    public function dateFormatToStrftime($dateFormat)
    {
        return strtr((string)$dateFormat, $this->getDateFormatToStrftimeMapping());
    }

    /**
     * Converts a strftime() format to a format usable by JDate
     *
     * @param string $strftime The dateformat in the strftime format
     *
     * @return string The date format in JDate usable format
     */
    public function strftimeToDateFormat($strftime)
    {
        return strtr((string)preg_replace("/(?<![\%|\\\\])(\w)/i", '\\\\$1', $strftime), array_flip($this->getDateFormatToStrftimeMapping()));
    }

    /**
     * Get an associative array that helds the mapping between strftime() and JDate formats
     *
     * @return array The mapping array
     */
    protected function getDateFormatToStrftimeMapping()
    {
        return array(
            // Day - no strf eq : S
            'd' => '%d', 'D' => '%a', 'j' => '%e', 'l' => '%A', 'N' => '%u', 'w' => '%w', 'z' => '%j',
            // Week - no date eq : %U, %W
            'W' => '%V',
            // Month - no strf eq : n, t
            'F' => '%B', 'm' => '%m', 'M' => '%b',
            // Year - no strf eq : L; no date eq : %C, %g
            'o' => '%G', 'Y' => '%Y', 'y' => '%y',
            // Time - no strf eq : B, G, u; no date eq : %r, %R, %T, %X
            'a' => '%P', 'A' => '%p', 'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M', 's' => '%S',
            // Timezone - no strf eq : e, I, P, Z
            'O' => '%z', 'T' => '%Z',
            // Full Date / Time - no strf eq : c, r; no date eq : %c, %D, %F, %x
            'U' => '%s'
        );
    }

    /**
     * Get the configured timezone offset
     *
     * If no user is given, it will return joomla general offset config, otherwise it will
     * return the offset set for the user
     *
     * @param \JUser $user The user for which we have to fetch the timezone offset (default: null)
     *
     * @return int The timezone offset
     */
    public function getOffset($user = null)
    {
        $user = $user == null ? $this->container->user->get() : $user;
        return $user->getParam('timezone', $this->container->system->config->get('offset'));
    }

    /**
     * @param $dateTime
     * @return \JDate
     */
    public function getDateOnly($dateTime)
    {
        // replace placeholders
        $value = $this->getFromPlaceholder($dateTime);
        $tzoffset = $this->getOffset();

        return $this->create($value, $tzoffset)->setTime(0, 0, 0);
    }

    /**
     * @param $dateTime
     * @return \JDate
     */
    public function getDateTime($dateTime)
    {
        // replace placeholders
        $value = $this->getFromPlaceholder($dateTime);
        $tzoffset = $this->getOffset();

        return $this->create($value, $tzoffset);
    }

    /**
     * Replace placeholders (if string and present) [yesterday] , [today] , [tomorrow] with a date
     *
     * @param mixed $value
     * @return mixed
     */
    public function getFromPlaceholder($value)
    {
        if ($this->isString($value)) {
            // init vars
            $tzoffset = $this->getOffset();

            switch (trim($value)) {
                case '[yesterday]':
                    $value = $this->create('yesterday', $tzoffset);
                    $value->setTime(0, 0, 0);
                    break;
                case '[today]':
                    $value = $this->create('today', $tzoffset);
                    $value->setTime(0, 0, 0);
                    break;
                case '[tomorrow]':
                    $value = $this->create('tomorrow', $tzoffset);
                    $value->setTime(0, 0, 0);
                    break;
            }
        }

        return $value;
    }

    /**
     * @param $value
     * @return array
     */
    public function getDayStart($value)
    {
        $date = $this->getDateOnly($value);
        $date->setTime(0, 0, 0);

        return $date;
    }

    /**
     * @param $value
     * @return array
     */
    public function getDayEnd($value)
    {
        $date = $this->getDateOnly($value);
        $date->setTime(23, 59, 59);

        return $date;
    }
}
