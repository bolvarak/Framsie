<?php
/**
 * This class provides an easy date/time manipulation system
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieDateTime {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the MySQL DATE format for the date function
	 * @var string
	 */
	const MYSQL_DATE_FORMAT           = 'Y-m-d';

	/**
	 * This constant contains the MySQL DATETIME/TIMESTAMP format for the date function
	 * @var string
	 */
	const MYSQL_DATETIME_FORMAT       = 'Y-m-d H:i:s';

	/**
	 * This constant contains the format for the first day of this month for the strtotime function
	 * @var string
	 */
	const STR_FIRST_DAY_OF_MONTH      = 'first day of this month';

	/**
	 * This constant contains the format for the first day of this week for the strtotime function
	 * @see This assumes Monday is the first day of the week
	 * @var string
	 */
	const STR_FIRST_DAY_THIS_WEEK_MON = 'monday this week';

	/**
	 * This constant contains the format for the first day of this week for the strtotime function
	 * @see this assumes Sunday is the first day of the week
	 * @var string
	 */
	const STR_FIRST_DAY_THIS_WEEK_SUN = 'sunday last week';

	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method calculates the timestamps for the days in this month
	 * @package Framsie
	 * @subpackage FramsieDateTime
	 * @access public
	 * @static
	 * @param integer $iTimeStamp
	 * @return array
	 */
	public static function CalculateDatesForMonth($iTimeStamp = null) {
		// Check for a timestamp
		if (empty($iTimeStamp)) {
			// Set the timestamp to the current time
			$iTimeStamp = time();
		}
		// Create a dates placeholder and calculate the date for the first day of this month
		$aDates = array(strtotime(self::STR_FIRST_DAY_OF_MONTH, $iTimeStamp));
		// Loop through the days
		for ($iDay = 0; $iDay < (date('t', $iTimeStamp) - 1); $iDay++) {
			// Add the date
			array_push($aDates, strtotime('+'.($iDay + 1).' days', $aDates[0]));
		}
		// Return the dates
		return $aDates;
	}

	/**
	 * This method calculates the timestamps for the days in a week
	 * @package Framsie
	 * @subpackage FramsieDateTime
	 * @access public
	 * @static
	 * @param string $sMode
	 * @param integer $iTimeStamp
	 * @return array
	 */
	public static function CalculateDatesForWeek($sMode = self::STR_FIRST_DAY_THIS_WEEK_SUN, $iTimeStamp = null) {
		// Check for a time stamp
		if (empty($iTimeStamp)) {
			// Set the timestamp to now
			$iTimeStamp = time();
		}
		// Setup the dates placeholder
		$aDates = array(strtotime($sMode, $iTimeStamp));
		// Loop through the days
		for ($iDay = 0; $iDay < 6; $iDay++) {
			// Add the date
			array_push($aDates, strtotime('+'.($iDay + 1).' days', $aDates[0]));
		}
		// Return the dates
		return $aDates;
	}

	/**
	 * This method calculates the timestamp for the days in a year
	 * @package Framsie
	 * @subpackage FramsieDateTime
	 * @access public
	 * @static
	 * @param integer $iTimeStamp
	 * @return array
	 */
	public static function CalculateDatesForYear($iTimeStamp = null) {
		// Check for a timestamp
		if (empty($iTimeStamp)) {
			// Set the timestamp
			$iTimeStamp = time();
		}
		// Grab the first day of the year
		$iFirstDay = strtotime(date('Y', $iTimeStamp).'-1-1',   $iTimeStamp);
		// Grab the last day of the year
		$iLastDay  = strtotime(date('Y', $iTimeStamp).'-12-31', $iTimeStamp);
		// Create the dates placeholder and add the first day of the year
		$aDates    = array($iFirstDay);
		// Loop through the days
		for ($iDay = 0; $iDay < date('z', $iLastDay); $iDay++) {
			// Add the date
			array_push($aDates, strtotime('+'.($iDay + 1).' days', $aDates[0]));
		}
		// Return the dates
		return $aDates;
	}

	/**
	 * This method returns a MySQL DATE
	 * @package Framsie
	 * @subpackage FramsieDateTime
	 * @access public
	 * @static
	 * @param integer $iTimeStamp
	 * @return string
	 */
	public static function GetMySqlDate($iTimeStamp = null) {
		// Check for a timestamp
		if (empty($iTimeStamp)) {
			// Set the timestamp
			$iTimeStamp = time();
		}
		// Return the MySQL date
		return date(self::MYSQL_DATE_FORMAT, $iTimeStamp);
	}

	/**
	 * This method returns a MySQL DATETIME/TIMESTAMP
	 * @package Framsie
	 * @subpackage FramsieDateTime
	 * @access public
	 * @static
	 * @param integer $iTimeStamp
	 * @return string
	 */
	public function GetMySqlTimeStamp($iTimeStamp = null) {
		// Check for a timestamp
		if (empty($iTimeStamp)) {
			// Set the timestamp
			$iTimeStamp = time();
		}
		// Return the MySQL timestamp
		return date(self::MYSQL_DATETIME_FORMAT, $iTimeStamp);
	}
}