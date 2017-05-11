<?php
/*************************************************
 * Titan-2 Mini Framework
 * Date Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT
 *
 *************************************************/
 namespace System\Libs\Date;

 class Date
 {

     /**
     * ATOM style date definition constant.
     * Ex. Output: 2014-02-21T20:55:30+02:00
     */
    const ATOM       = "Y-m-d\TH:i:sP" ;

    /**
     * COOKIE style date definition constant.
     * Ex. Output: Friday, 21-Feb-14 20:56:21 EET
     */
    const COOKIE     = "l, d-M-y H:i:s T" ;

    /**
     * ISO8601 style date definition constant.
     * Ex. Output: 2014-02-21T20:57:15+0200
     */
    const ISO8601    = "Y-m-d\TH:i:sO" ;

    /**
     * RFC822 style date definition constant.
     * Ex. Output: Fri, 21 Feb 2014 20:58:24 +0200
     */
    const RFC822     = "D, d M y H:i:s O" ;

    /**
     * RFC850 style date definition constant.
     * Ex. Output: Friday, 21-Feb-14 20:59:23 EET
     */
    const RFC850     = "l, d-M-y H:i:s T" ;

    /**
     * RFC1036 style date definition constant.
     * Ex. Output: Fri, 21 Feb 14 21:00:17 +0200
     */
    const RFC1036    = "D, d M y H:i:s O" ;

    /**
     * RFC1123 style date definition constant.
     * Ex. Output: Fri, 21 Feb 2014 21:00:58 +0200
     */
    const RFC1123    = "D, d M Y H:i:s O" ;

    /**
     * RFC2822 style date definition constant.
     * Ex. Output: Fri, 21 Feb 2014 21:01:35 +0200
     */
    const RFC2822    = "D, d M Y H:i:s O" ;

    /**
     * RFC3339 style date definition constant.
     * Ex. Output: 2014-02-21T21:02:31+02:00
     */
    const RFC3339    = "Y-m-d\TH:i:sP" ;

    /**
     * RSS style date definiton constant.
     * Ex. Output: Fri, 21 Feb 2014 21:03:26 +0200
     */
    const RSS        = "D, d M Y H:i:s O" ;

    /**
     * W3C style date definiton constant.
     * Ex. Output: 2014-02-21T21:04:09+02:00
     */
    const W3C        = "Y-m-d\TH:i:sP" ;

    /**
     * GENERIC style date definition constant.
     * Ex. Output: 2014-02-21 21:04:55
     */
    const GENERIC    = "Y-m-d H:i:s" ;

    /**
     * @var String holds the timestamp value of the initialized date.
     */
    private $timestamp = '';

    /**
     *
     * @var String holds the comparison date value as timestamp.
     */
    private $comparisonDateTimestamp = '';

    /**
     * @var Array holds the date comparison results as an array.
     */
    private $comparisonArray = [];

    /**
     * Holds the localization string.
     * @var string localization string variable.
     */
    private $locale = 'en_EN.UTF-8';

    /**
     * Constructor method.
     */
    public function __construct($locale = 'en_EN.UTF-8')
    {
        if ($locale != '') {
            $this->locale = $locale;
        }

        setlocale(LC_TIME, $this->locale);
    }

    /**
     * Called when object is directly printed.
     *
     * @return string resultant date string by calling the get() method.
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * Returns the date as a string.
     *
     * @example
     * <code>
     * $date = new SimpleDate();
     * echo $date->now()->get(); //print 2013
     * </code>
     * @param string date definition format. EX: 'yyyy-mm-dd'. You can use predifined class constant. Ex: simpleDate::RSS
     * @return string formatted date string Ex: 2013-12-21 13:21:58
     */
    public function get($format = Date::GENERIC)
    {
        $this->controlTimestamp();
        //return strftime($format, $this->timestamp);
        return date($format, $this->timestamp);
    }

    /**
     * Get the year part of the date.
     *
     * @return int year part of the date. ex: 2012
     */
    public function getYear()
    {
        $this->controlTimestamp();
        return date("Y", $this->timestamp);
    }

    /**
     * Get the month part of the year.
     *
     * @param boolean $withZero true or false. default is true. Make false to get the month without leading 0's.
     * @return string month part of the date. Ex: 03 (if withZero is true), 3 (if withZero is false.)
     */
    public function getMonth($withZero = true)
    {
        $this->controlTimestamp();
        if ($withZero)
            return date("m", $this->timestamp);
        else
            return date("n", $this->timestamp);
    }

    /**
     * Get the month part of the date as a localized string.
     *
     * @param boolean $isShort true or false. When true the month string will be shortened
     * to 3 letters, Ex: Jul for July. When false, The all month string will return, Ex: January.
     * Default value is false.
     * @return string month part of the date as a string. Ex: January or Jan. If you want
     * to get the localized string value of the month, you can initialize the simpleDate() class
     * with localization parameter. Please refer to documentation for further information.
     */
    public function getMonthString($isShort = false)
    {
        $this->controlTimestamp();
        if ($isShort)
            return strftime("%b", $this->timestamp);
        else
            return strftime("%B", $this->timestamp);
    }

    /**
     * Get the day part of the date. Ex: 21
     *
     * @param boolean $withZero true or false. When true, the output will be with leading zero.
     * Ex: 03. When false, the output will not contain leading 0. Ex: 3. Default is true.
     * @return string the day part of the date. Ex: 03 or 3.
     */
    public function getDay($withZero = true)
    {
        $this->controlTimestamp();
        if ($withZero)
            return date("d", $this->timestamp);
        else
            return date("j", $this->timestamp);
    }

    /**
     * Get the day part of the date as a localized string.
     *
     * @param boolean $isShort true or false. When true the day string will be shortened
     * to 3 letters, Ex: Fri for Friday. When false, The all day string will return, Ex: Friday.
     * Default value is false.
     * @return string day part of the date as a string. Ex: Fri or Friday. If you want
     * to get the localized string value of the day, you can initialize the simpleDate() class
     * with localization parameter. Please refer to documentation for further information.
     */
    public function getDayString($isShort = false)
    {
        $this->controlTimestamp();
        if ($isShort)
            return strftime("%a", $this->timestamp);
        else
            return strftime("%A", $this->timestamp);
    }

    /**
     * The hour part of the date.
     *
     * @param int $mode 12 or 24. if set to 12, then the resultant value will be 11 for 23 o'clock.
     * default value is 24.
     * @param boolean $withZero true or false. When true, the hour will have
     * the leading zero. Ex: 09. When false, the hour will not have the leading zero Ex: 9.
     * @return string the hour part of the date.
     */
    public function getHour($mode = 24, $withZero = true)
    {
        $this->controlTimestamp();
        if ($mode == 24) {
            if ($withZero)
                return date("H", $this->timestamp);
            else
                return date("G", $this->timestamp);
        } else {
            if ($withZero)
                return date("h", $this->timestamp);
            else
                return date("g", $this->timestamp);
        }
    }

    /**
     * Get the minute part of the date.
     *
     * @return int the minute part of the date. Ex: 58
     */
    public function getMinute()
    {
        $this->controlTimestamp();
        return date("i", $this->timestamp);
    }

    /**
     * Get the second part of the date.
     *
     * @return int the second part of the datetime. Ex: 38
     */
    public function getSecond()
    {
        $this->controlTimestamp();
        return date("s", $this->timestamp);
    }

    /**
     * Get the milisecond part of the date
     *
     * @return int the milisecond part of the datetime.
     */
    public function getMiliSecond()
    {
        $this->controlTimestamp();
        return date("u", $this->timestamp);
    }

    /**
     * Get the timestamp value of datetime.
     *
     * @return int The timestamp value. Ex: 1393011488
     */
    public function getTimestamp()
    {
        $this->controlTimestamp();
        return $this->timestamp;
    }

    /**
     * Get the day of the week.
     * 0 for sunday, 6 for saturday. Ex: 1 for Monday.
     *
     * @return int the day of the week. From 0 to 6.
     */
    public function getDayOfWeek()
    {
        $this->controlTimestamp();
        return date('w', $this->timestamp);
    }

    /**
     * Get the day of the selected date.
     *
     * @return int the day of the year. From 0 to 364 (365 for leap year.)
     */
    public function getDayOfYear()
    {
        $this->controlTimestamp();
        return date('z', $this->timestamp);
    }

    /**
     * Get the week number for the selected year.
     *
     * @return int the week number of the year. from 0 to 52.
     */
    public function getWeekOfYear()
    {
        $this->controlTimestamp();
        return date('W', $this->timestamp);
    }

    /**
     * Get how many days available in the defined month.
     *
     * @return int the days in month. 28 or 29 or 30 or 31.
     */
    public function getDaysInMonth()
    {
        $this->controlTimestamp();
        return date('t', $this->timestamp);
    }

    /**
     * Learn whether the defined year is a leap year.
     *
     * @return int 1 or 0. 1 indicates that the year is a leap year.
     */
    public function isLeapYear()
    {
        $this->controlTimestamp();
        return date('L', $this->timestamp);
    }

    /**
     * Initialize the date as the current system datetime.
     *
     * @return object simpleDate object.
     */
    public function now()
    {
        $this->timestamp = strtotime('now');
        return $this;
    }

    /**
     * Initialize the date from the given date string.
     *
     * @param string $dateString Ex: '2012-12-31' OR '2012-12-31 23:59:59'
     * OR you can define any string that php's strtotime() function can accept.
     * Please refer to strtotime() manual for further information.
     * @return object simpleDate object
     */
    public function set($dateString)
    {
        $this->timestamp = strtotime($dateString);
        return $this;
    }

    /**
     * Initialze the date from a timestamp. Ex:1393011488
     *
     * @param int $timestamp the timestamp value of the date.
     * @return object simpleDate object.
     */
    public function setFromTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * Set the year part of the initialized date.
     *
     * @param int $year Ex: 1994
     * @return object simpleDate object
     */
    public function setYear($year)
    {
        $month  = $this->getMonth();
        $day    = $this->getDay();
        $hour   = $this->getHour();
        $minute = $this->getMinute();
        $second = $this->getSecond();

        $this->timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        return $this;
    }

    /**
     * Set the month part of the initialized date.
     *
     * @param int $month Ex: 3
     * @return object simpleDate object
     */
    public function setMonth($month)
    {
        $year   = $this->getYear();
        $day    = $this->getDay();
        $hour   = $this->getHour();
        $minute = $this->getMinute();
        $second = $this->getSecond();

        $this->timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        return $this;
    }

    /**
     * Set the day part of the initialized date.
     *
     * @param int $day Ex: 9
     * @return object simpleDate object
     */
    public function setDay($day)
    {
        $year   = $this->getYear();
        $month  = $this->getMonth();
        $hour   = $this->getHour();
        $minute = $this->getMinute();
        $second = $this->getSecond();

        $this->timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        return $this;
    }

    /**
     * Set the hour part of the initialized date.
     *
     * @param int $hour Ex: 1
     * @return object simpleDate object
     */
    public function setHour($hour)
    {
        $year   = $this->getYear();
        $month  = $this->getMonth();
        $day    = $this->getDay();
        $minute = $this->getMinute();
        $second = $this->getSecond();

        $this->timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        return $this;
    }

    /**
     * Set the minute part of the initialized date.
     *
     * @param int $minute Ex: 3
     * @return object simpleDate object
     */
    public function setMinute($minute)
    {
        $year   = $this->getYear();
        $month  = $this->getMonth();
        $day    = $this->getDay();
        $hour   = $this->getHour();
        $second = $this->getSecond();

        $this->timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        return $this;
    }

    /**
     * Set the second part of the initialized date.
     *
     * @param int $second Ex: 0
     * @return object simpleDate object
     */
    public function setSecond($second)
    {
        $year   = $this->getYear();
        $month  = $this->getMonth();
        $day    = $this->getDay();
        $hour   = $this->getHour();
        $minute = $this->getMinute();

        $this->timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        return $this;
    }

    /**
     * Add years to initialized date.
     *
     * @param int $year the amount of year to add. Ex: 10
     * @return object simpleDate object
     */
    public function addYear($year)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("+".$year." year", $this->timestamp);
        return $this;
    }

    /**
     * Add months to initialized date.
     *
     * @param int $month the amount of month to add. Ex: 10
     * @return object simpleDate object
     */
    public function addMonth($month)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("+".$month." month", $this->timestamp);
        return $this;
    }

    /**
     * Add days to initialized date.
     *
     * @param int $day the amount of day to add. Ex: 40
     * @return object simpleDate object
     */
    public function addDay($day)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("+".$day." day", $this->timestamp);
        return $this;
    }

    /**
     * Add hours to initialized date.
     *
     * @param int $hour the amount of hour to add. Ex: 72
     * @return object simpleDate object
     */
    public function addHour($hour)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("+".$hour." hour", $this->timestamp);
        return $this;
    }

    /**
     * Add minute to initialized date.
     *
     * @param int $minute the amount of minute to add. Ex: 120
     * @return object simpleDate object
     */
    public function addMinute($minute)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("+".$minute." minute", $this->timestamp);
        return $this;
    }

    /**
     * Add seconds to initialized date.
     *
     * @param int $second the amount of second to add. Ex: 3600
     * @return object simpleDate object
     */
    public function addSecond($second)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("+".$second." second", $this->timestamp);
        return $this;
    }

    /**
     * Subtract years from initialized date.
     *
     * @param int $year the amount of year to subtract. Ex: 10
     * @return object simpleDate object
     */
    public function subtractYear($year)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("-".$year." year", $this->timestamp);
        return $this;
    }

    /**
     * Subtract months from initialized date.
     *
     * @param int $month the amount of month to subtract. Ex: 6
     * @return object simpleDate object
     */
    public function subtractMonth($month)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("-".$month." month", $this->timestamp);
        return $this;
    }

    /**
     * Subtract days from initialized date.
     *
     * @param int $day the amount of day to subtract. Ex: 30
     * @return object simpleDate object
     */
    public function subtractDay($day)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("-".$day." day", $this->timestamp);
        return $this;
    }

    /**
     * Subtract hours from initialized date.
     *
     * @param int $hour the amount of hour to subtract. Ex: 72
     * @return object simpleDate object
     */
    public function subtractHour($hour)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("-".$hour." hour", $this->timestamp);
        return $this;
    }

    /**
     * Subtract minute from initialized date.
     *
     * @param int $minute the amount of minute to subtract. Ex: 120
     * @return object simpleDate object
     */
    public function subtractMinute($minute)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("-".$minute." minute", $this->timestamp);
        return $this;
    }

    /**
     * Subtract seconds from initialized date.
     *
     * @param int $second the amount of second to subtract. Ex: 3600
     * @return object simpleDate object
     */
    public function subtractSecond($second)
    {
        $this->controlTimestamp();
        $this->timestamp = strtotime("-".$second." second", $this->timestamp);
        return $this;
    }

    /**
     * Compare the given date string with the initialized date.
     * Example usage:
     * <code>
     * $date = new simpleDate();
     * echo $date->now()->compare('2013-01-01')->isBefore();
     * </code>
     *
     * @param string $dateString the string of the date to compare. Ex: '2013-12-03'
     * OR '2013-01-01 13:21:58' OR any string that php's strtotime() function can accept.
     * Please refer to strtotime() documentation.
     * @return object  simpleDate object
     */
    public function compare($dateString)
    {
        $this->comparisonDateTimestamp = strtotime($dateString);
        $this->calculateDifference();
        return $this;
    }

    /**
     * Compare the given date timestamp with the initialized date.
     * Example usage:
     * <code>
     * $date = new simpleDate();
     * echo $date->now()->compareTimestamp('1393011488')->isBefore();
     * </code>
     *
     * @param string $dateString the string of the date to compare. Ex: 1393011488
     * @return object  simpleDate object
     */
    public function compareTimestamp($timestamp)
    {
        $this->comparisonDateTimestamp = $timestamp;
        $this->calculateDifference();
        return $this;
    }

    /**
     * Get the result of comparison as an array.
     * This array will include the following definitions as the comparsion result:
     * <code>
     *    $result["y"] = the year difference
     *    $result["m"] = month difference
     *    $result["d"] = day difference
     *    $result["h"] = hour difference
     *    $result["i"] = minute difference
     *    $result["s"] = second difference
     *    $result["isBefore"] = is initialized date before the comparison date. 1 or 0.
     *    $result["days"] = total number of days between compared dates.
     * </code>
     *
     * @return array the result of the comparison as an array.
     */
    public function getComparisonArray()
    {
        return $this->comparisonArray;
    }

    /**
     * Get the difference between dates in years.
     *
     * @return int the comparison result in years.
     */
    public function getComparisonInYears()
    {
        return $this->comparisonArray['y'];
    }

    /**
     * Get the difference between dates in months.
     *
     * @return int the comparison result in months.
     */
    public function getComparisonInMonths()
    {
        return $this->comparisonArray['m'] + ($this->getComparisonInYears() * 12);
    }

    /**
     * Get the difference between dates in days.
     *
     * @return int the comparison result in days.
     */
    public function getComparisonInDays()
    {
        return $this->comparisonArray['days'];
    }

    /**
     * Get the difference between dates in hours.
     *
     * @return int the comparison result in hours.
     */
    public function getComparisonInHours()
    {
        return $this->comparisonArray['h'] + ($this->getComparisonInDays() * 24);
    }

    /**
     * Get the difference between dates in minutes.
     *
     * @return int the comparison result in minutes.
     */
    public function getComparisonInMinutes()
    {
        return $this->comparisonArray['i'] + ($this->getComparisonInHours() * 60);
    }

    /**
     * Get the difference between dates in seconds.
     *
     * @return int the comparison result in seconds.
     */
    public function getComparisonInSeconds()
    {
        return $this->comparisonArray['s'] + ($this->getComparisonInMinutes() * 60);
    }

    /**
     * Get whether the initialized date is before the compared date.
     *
     * @return int 1 or 0. 1 indicates that the initialized date is before the compared date.
     */
    public function isBefore()
    {
        return $this->comparisonArray['isBefore'];
    }

    /**
     * Get whether the initialized date is equal to the compared date.
     *
     * @return int 1 or 0. 1 indicates that the initialized date is equal to the compared date.
     */
    public function isEqual()
    {
        if ($this->comparisonArray['y'] == 0 &&
            $this->comparisonArray['m'] == 0 &&
            $this->comparisonArray['d'] == 0 &&
            $this->comparisonArray['h'] == 0 &&
            $this->comparisonArray['i'] == 0 &&
            $this->comparisonArray['s'] == 0)
            return 1;
        else
            return 0;
    }

    /**
     * Get whether the initialized date is before or equal to the compared date.
     *
     * @return int 1 or 0. 1 indicates that the initialized date is before or equal to the compared date.
     */
    public function isBeforeOrEqual()
    {
        if ($this->comparisonArray['isBefore'] || $this->isEqual())
            return 1;
        else
            return 0;
    }

    /**
     * Get whether the initialized date is after or equal to the compared date.
     *
     * @return int 1 or 0. 1 indicates that the initialized date is after or equal the compared date.
     */
    public function isAfterOrEqual()
    {
        if (!$this->comparisonArray['isBefore'] || $this->isEqual())
            return 1;
        else
            return 0;
    }

    /**
     * Get time differance as readable format
     *
     * @param string $time
     * @return string
     */
    public function humantime($time = null)
    {
        if (is_null($time))
            $time = $this->timestamp;
        else
    	   $time   = strtotime($time);

    	$time_diff = time() - $time;
    	$second    = $time_diff;
    	$minute    = round($time_diff / 60);
    	$hour      = round($time_diff / 3600);
    	$day       = round($time_diff / 86400);
    	$week      = round($time_diff / 604800);
    	$month     = round($time_diff / 2419200);
    	$year      = round($time_diff / 29030400);

    	if ($second < 60) {
    		if ($second == 0) {
    			return lang('date', 'just');
    		} else {
    			return lang('date', 'seconds_ago', $second);
    		}
    	} else if ($minute < 60) {
    		return lang('date', 'minues_ago', $minute);
    	} else if ($hour < 24) {
    		return lang('date', 'hours_ago', $hour);
    	} else if ($day < 7) {
    		return lang('date', 'days_ago', $day);
    	} else if ($week < 4) {
    		return lang('date', 'weeks_ago', $week);
    	} else if ($month < 12) {
    		return lang('date', 'months_ago', $month);
    	} else {
    		return lang('date', 'years_ago', $year);
    	}
    }

    /**
     * Private function to calculate the difference between two dates
     * and create the comparison array.
     */
    private function calculateDifference()
    {
        $one    = $this->timestamp;
        $two    = $this->comparisonDateTimestamp;
        $invert = false;

        if ($one > $two) {
            list($one, $two) = [$two, $one];
            $invert = true;
        }

        $key    = ["y", "m", "d", "h", "i", "s"];
        $a      = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
        $b      = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));

        $result = [];
        $result["y"] = $b["y"] - $a["y"];
        $result["m"] = $b["m"] - $a["m"];
        $result["d"] = $b["d"] - $a["d"];
        $result["h"] = $b["h"] - $a["h"];
        $result["i"] = $b["i"] - $a["i"];
        $result["s"] = $b["s"] - $a["s"];
        $result["isBefore"] = $invert ? 0 : 1;
        $result["days"] = intval(abs(($one - $two)/86400));

        if ($invert)
            $this->dateNormalize($a, $result);
        else
            $this->dateNormalize($b, $result);

        $this->comparisonArray = $result;
    }

    /**
     * Private function to control the timestamp.
     * If it is not initialized, initializes it to now() value.
     */
    private function controlTimestamp()
    {
        if ($this->timestamp == '')
            $this->now();
    }

    private function dateRangeLimit($start, $end, $adj, $a, $b, &$result)
    {
        if ($result[$a] < $start) {
            $result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
            $result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
        }

        if ($result[$a] >= $end) {
            $result[$b] += intval($result[$a] / $adj);
            $result[$a] -= $adj * intval($result[$a] / $adj);
        }

        return $result;
    }

    private function dateRangeLimitDays(&$base, &$result)
    {
        $days_in_month_leap = [31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $days_in_month      = [31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        $this->DateRangeLimit(1, 13, 12, "m", "y", $base);

        $year   = $base["y"];
        $month  = $base["m"];

        if ($result["isBefore"]) {
            while ($result["d"] < 0) {
                $month--;
                if ($month < 1) {
                    $month += 12;
                    $year--;
                }

                $leapyear   = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days       = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $result["d"] += $days;
                $result["m"]--;
            }
        } else {
            while ($result["d"] < 0) {
                $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $result["d"] += $days;
                $result["m"]--;

                $month++;
                if ($month > 12) {
                    $month -= 12;
                    $year++;
                }
            }
        }

        return $result;
    }

    private function dateNormalize(&$base, &$result)
    {
        $result = $this->dateRangeLimit(0, 60, 60, "s", "i", $result);
        $result = $this->dateRangeLimit(0, 60, 60, "i", "h", $result);
        $result = $this->dateRangeLimit(0, 24, 24, "h", "d", $result);
        $result = $this->dateRangeLimit(0, 12, 12, "m", "y", $result);

        $result = $this->dateRangeLimitDays($base, $result);

        $result = $this->dateRangeLimit(0, 12, 12, "m", "y", $result);

        return $result;
    }

 }