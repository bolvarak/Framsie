<?php
/**
 * This class provides an array of conversion utilities for quick access
 * @package Framsie
 * @subpackage FramsieConverter
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieConverter {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant provides the definition for BASE-1 math
	 * @var integer
	 */
	const BASE_UNARY                        = 1;

	/**
	 * This constant provides the definition for BASE-2 math
	 * @var integer
	 */
	const BASE_BINARY                       = 2;

	/**
	 * This constant provides the definition for BASE-3 math
	 * @var integer
	 */
	const BASE_TERNARY                      = 3;

	/**
	 * This constant provides the definition for BASE-4 math
	 * @var integer
	 */
	const BASE_QUATERNARY                   = 4;

	/**
	 * This constant provides the definition for BASE-5 math
	 * @var integer
	 */
	const BASE_QUINARY                      = 5;

	/**
	 * This constant provides the definition for BASE-6 math
	 * @var integer
	 */
	const BASE_SENARY                       = 6;

	/**
	 * This constant provides the definition for BASE-7 math
	 * @var integer
	 */
	const BASE_SEPTENARY                    = 7;

	/**
	 * This constant provides the definition for BASE-8 math
	 * @var integer
	 */
	const BASE_OCTAL                        = 8;

	/**
	 * This constant provides the definition for BASE-9 math
	 * @var integer
	 */
	const BASE_NONARY                       = 9;

	/**
	 * This constant provides the definition for BASE-10 math
	 * @var integer
	 */
	const BASE_DECIMAL                      = 10;

	/**
	 * This constant provides the definition for BASE-11 math
	 * @var integer
	 */
	const BASE_UNDENARY                     = 11;

	/**
	 * This constant provides the definition for BASE-12 math
	 * @var integer
	 */
	const BASE_DUODECIMAL                   = 12;

	/**
	 * This constant provides the definition for BASE-13 math
	 * @var integer
	 */
	const BASE_TRIDECIMAL                   = 13;

	/**
	 * This constant provides the definition for BASE-14 math
	 * @var integer
	 */
	const BASE_QUATTUORDECIMAL              = 14;

	/**
	 * This constant provides the definition for BASE-15 math
	 * @var integer
	 */
	const BASE_QUINDECIMAL                  = 15;

	/**
	 * This constant provides the definition for BASE-16 math
	 * @var integer
	 */
	const BASE_HEXADECIMAL                  = 16;

	/**
	 * This constant provides the definition for BASE-17 math
	 * @var integer
	 */
	const BASE_SEPTENDECIMAL                = 17;

	/**
	 * This constant provides the definition for BASE-18 math
	 * @var integer
	 */
	const BASE_OCTODECIMAL                  = 18;

	/**
	 * This constant provides the definition for BASE-19 math
	 * @var integer
	 */
	const BASE_NONADECIMAL                  = 19;

	/**
	 * This constant provides the definition for BASE-20 math
	 * @var integer
	 */
	const BASE_VIGESIMAL                    = 20;

	/**
	 * This constant provides the definition for BASE-30 math
	 * @var integer
	 */
	const BASE_TRIGESIMAL                   = 30;

	/**
	 * This constant provides the definition for BASE-40 math
	 * @var integer
	 */
	const BASE_QUADRAGESIMAL                = 40;

	/**
	 * This constant provides the definition for BASE-50 math
	 * @var integer
	 */
	const BASE_QUINQUAGESIMAL               = 50;

	/**
	 * This constant provides the definition for BASE-60 math
	 * @var integer
	 */
	const BASE_SEXAGESIMAL                  = 60;

	/**
	 * This constant provides the definition for BASE-70 math
	 * @var integer
	 */
	const BASE_SEPTAGESIMAL                 = 70;

	/**
	 * This constant provides the definition for BASE-80 math
	 * @var integer
	 */
	const BASE_OCTAGESIMAL                  = 80;

	/**
	 * This constant provides the definition for BASE-90 math
	 * @var integer
	 */
	const BASE_NONAGESIMAL                  = 90;

	/**
	 * This constant provides the definition for BASE-100 math
	 * @var integer
	 */
	const BASE_CENTIMAL                     = 100;

	/**
	 * This constant provides the definition for BASE-200 math
	 * @var integer
	 */
	const BASE_BICENTIMAL                   = 200;

	/**
	 * This constant provides the definition for BASE-300 math
	 * @var integer
	 */
	const BASE_TEROCENTIMAL                 = 300;

	/**
	 * This constant provides the definition for BASE-400 math
	 * @var integer
	 */
	const BASE_QUATTROCENTIMAL              = 400;

	/**
	 * This constant provides the definition for BASE-500 math
	 * @var integer
	 */
	const BASE_QUINCENTIMAL                 = 500;

	/**
	 * This constant provides the definition for BASE-1000 math
	 * @var integer
	 */
	const BASE_MILLENARY                    = 1000;

	/**
	 * This constant contains the number of bytes in a megabyte
	 * @var integer
	 */
	const BYTES_IN_MEGABYTE                 = 1048576;

	/**
	 * This constant contains the number of centimeters in one foot
	 * @var float
	 */
	const CENTIMETERS_IN_FOOT               = 30.48;

	/**
	 * This constant contains the number of centimeters in one inch
	 * @var float
	 */
	const CENTIMETERS_IN_INCH               = 2.54;

	/**
	 * This constant contains the number of inches one centimeter
	 * @var float
	 */
	const INCHES_IN_CENTIMETER              = 0.3937;

	/**
	 * This constant contains the number of inches in one foot
	 * @var integer
	 */
	const INCHES_IN_FOOT                    = 12;

	/**
	 * This constant contains the number of kilograms in one pound
	 * @var float
	 */
	const KILOGRAMS_IN_POUND                = 0.454;

	/**
	 * This constant contains the number of minutes in one hour
	 * @var integer
	 */
	const MINUTES_IN_HOUR                   = 60;

	/**
	 * This constant contains the number of ounces in one cup
	 * @var integer
	 */
	const OUNCES_IN_CUP                     = 8;

	/**
	 * This constant contains the number of pounds in one kilogram
	 * @var float
	 */
	const POUNDS_IN_KILOGRAM                = 2.205;

	/**
	 * This constant contains the number of seconds in one day
	 * @var integer
	 */
	const SECONDS_IN_DAY                    = 86400;

	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method changes the base math of the specified number
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float|integer|number|string $mNumber
	 * @param integer $iBase
	 * @param integer $iToBase
	 * @return string
	 */
	public static function BaseConvert($mNumber, $iBase, $iToBase) {
		// Return the converted number
		return base_convert($mNumber, $iBase, $$iToBase);
	}

	/**
	 * This method converts a binary string to a hexadecimal string
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param string $sBinary
	 * @return string
	 */
	public static function BinToHex($sBinary) {
		// Return the converted string
		return bin2hex($sBinary);
	}

	/**
	 * This method converts bytes to megabytes
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iBytes
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function BytesToMegabytes($iBytes, $iDecimals = 2) {
		// Return the converted value
		return round(($iBytes / self::BYTES_IN_MEGABYTE), $iDecimals);
	}

	/**
	 * This method converts centimeters into feet
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iCentimeters
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function CentimetersToFeet($iCentimeters, $iDecimals = 2) {
		// Return the converted value
		return round(($iCentimeters / self::CENTIMETERS_IN_FOOT), $iDecimals);
	}

	/**
	 * This method converts centimeters into inches
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iCentimeters
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function CentimetersToInches($iCentimeters, $iDecimals = 2) {
		// Return the converted value
		return round(($iCentimeters / self::CENTIMETERS_IN_INCH), $iDecimals);
	}

	/**
	 * This method converts cups into ounces
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iCups
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function CupsToOunces($iCups, $iDecimals = 2) {
		// Return the converted value
		return round(($iCups * self::OUNCES_IN_CUP), $iDecimals);
	}

	/**
	 * This method converts a timestamp to an age in years
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param integer $iTimeStamp
	 * @return integer
	 */
	public static function DateToAge($iTimeStamp) {
		// Create the DateTime object
		$oDate        = new DateTime(date('Y-m-d H:i:s', $iTimeStamp));
		// Create the current DateTime object
		$oCurrentDate = new DateTime(date('Y-m-d H:i:s', time()));
		// Calculate the difference
		$oInterval    = $oDate->diff($oCurrentDate, true);
		// Return the difference
		return intval($oInterval->format('%y'));
	}

	/**
	 * This method returns the UNIX timestamp of the number of days since Epoch
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param integer $iDays
	 * @return integer
	 */
	public static function DaysInEpoch($iDays) {
		// Return the converted value
		return strtotime("January 1, 1970 + {$iDays} days");
	}

	/**
	 * This method converts time() to days
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param integer $iTimeStamp
	 * @return integer
	 */
	public static function EpochInDays($iTimeStamp = null) {
		// Check for a timestamp
		if (empty($iTimeStamp)) {
			// Set the timestamp to the current time
			$iTimeStamp = time();
		}
		// Grab the days
		// Return the converted value
		return strtotime('+'.($iTimeStamp / self::SECONDS_IN_DAY).' days', 0);
	}

	/**
	 * This method converts feet into centimeters
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iFeet
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function FeetToCentimeters($iFeet, $iDecimals = 2) {
		// Return the converted value
		return round(($iFeet * self::CENTIMETERS_IN_FOOT), $iDecimals);
	}

	/**
	 * This method converts feet into inches
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iFeet
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function FeetToInches($iFeet, $iDecimals = 2) {
		// Return the converted value
		return round(($iFeet * self::INCHES_IN_FOOT), $iDecimals);
	}

	/**
	 * This method converts a hexidecimal number to an integer
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param integer $sHexadecimal
	 * @return integer
	 */
	public static function HexadecimalToInteger($sHexadecimal) {
		// Return the converted hex
		return hexdec($sHexadecimal);
	}

	/**
	 * This method converts a hexadecimal string back into a binary string
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param string $sHexadecimal
	 * @return string
	 */
	public static function HexToBin($sHexadecimal) {
		// Return the converted string
		return pack('H*', $sHexadecimal);
	}

	/**
	 * This method converts hours to minutes
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iHours
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function HoursToMinutes($iHours, $iDecimals = 2) {
		// Return the converted value
		return round(($iHours * self::MINUTES_IN_HOUR), $iDecimals);
	}

	/**
	 * This method converts inches into centimeters
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iInches
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function InchesToCentimeters($iInches, $iDecimals = 2) {
		// Return the converted value
		return round(($iInches / self::INCHES_IN_CENTIMETER), $iDecimals);
	}

	/**
	 * This method converts inches into feet
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iInches
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function InchesToFeet($iInches, $iDecimals = 2) {
		// Return the converted value
		return round(($iInches / self::INCHES_IN_FOOT), $iDecimals);
	}

	/**
	 * This method converts an integer to hexadecimal
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param integer $iInteger
	 * @return string
	 */
	public static function IntegerToHexadecimal($iInteger) {
		// Return the converted integer
		return dechex($iInteger);
	}

	/**
	 * This method converts kilograms into pounds
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iKilograms
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function KilogramsToPounds($iKilograms, $iDecimals = 2) {
		// Return the converted value
		return round(($iKilograms / self::KILOGRAMS_IN_POUND), $iDecimals);
	}

	/**
	 * This method converts minutes into hours
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iMinutes
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function MinutesToHours($iMinutes, $iDecimals = 2) {
		// Return the converted value
		return round(($iMinutes / self::MINUTES_IN_HOUR), $iDecimals);
	}

	/**
	 * This method converts ounces to cups
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iOunces
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function OuncesToCups($iOunces, $iDecimals = 2) {
		// Return the converted value
		return round(($iOunces / self::OUNCES_IN_CUP), $iDecimals);
	}

	/**
	 * This method converts pounds into kilograms
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param float $iPounds
	 * @param integer $iDecimals
	 * @return float
	 */
	public static function PoundsToKilograms($iPounds, $iDecimals = 2) {
		// Return the converted value
		return round(($iPounds / self::POUNDS_IN_KILOGRAM), $iDecimals);
	}

	/**
	 * This method rounds a number to the nearest multiple
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @param integer $iNumber
	 * @param integer $iMultiple
	 * @param boolean $bRoundUp
	 * @param integer $iDecimals
	 * @return integer
	 */
	public function RoundToNearestMultiple($iNumber, $iMultiple, $bRoundUp = true, $iDecimals = 2) {
		// Check for a zero multiple
		if ($iMultiple === 0) {
			// Return the number
			return $iNumber;
		}
		// Calculate the remainder
		$iRemainder = ($iNumber % $iMultiple);
		// Determine if we have a zero remainder
		if ($iRemainder === 0) {
			// Simply return the number
			return $iNumber;
		}
		// Caclulate the rounded up number
		$iRoundedUp   = ($iNumber + $iMultiple - $iRemainder);
		// Calculate the rouned down number
		$iRoundedDown = ($iRoundedUp - $iMultiple);
		// Determine if it makes sense to round up
		if (($iRoundedUp - $iNumber) < ($iNumber - $iRoundedDown)) {
			// Return the rounded up number
			return round($iRoundedUp, $iDecimals);
		}
		// Determine if it makes sense to round down
		if (($iNumber - $iRoundedDown) < ($iRoundedUp - $iNumber)) {
			// Return the rounded down number
			return round($iRoundedDown, $iDecimals);
		}
		// If we get here, fall back to what the user wants
		return round((($bRoundUp === true) ? $iRoundedUp : $iRoundedDown), $iDecimals);
	}

	/**
	 * This method converts a string represented value to its actual PHP type
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param string $sValue
	 * @return boolean|float|integer|null|string
	 */
	public static function StringToPhpType($sValue) {
		// Make sure the value is of string type
		if (is_string($sValue)) {
			// Trim the value
			$sValue = (string) trim($sValue);
			// Check the variable type
			if (preg_match('/^false|true$/', $sValue)) {
				// Return the variable as a boolean
				return (boolean) (($sValue === 'true') ? true : false);
			}
			if (preg_match('/^[0-9]+\.[0-9]+$/', $sValue)) {
				// Return the variable as a floating point
				return (float) floatval($sValue);
			}
			if (preg_match('/^[0-9]+$/', $sValue)) {
				// Return the variable as an integer
				return (integer) intval($sValue);
			}
			// Check for null
			if (empty($sValue) || is_null($sValue) || preg_match('/^null|nil$/i', $sValue)) {
				// Return the variable as a null
				return null;
			}
			// Elsewise return a string
			return (string) $sValue;
		}
		// Elsewise return the value
		return $sValue;
	}

	/**
	 * This method converts variable names to Hungarian Upper Camel Case notation
	 * @package Framsie
	 * @subpackage FramsieConverter
	 * @access public
	 * @static
	 * @param string $sVariableName
	 * @return string
	 */
	public static function VariableNameToHungarianUpperCamelCase($sVariableName) {
		// Lowercase the string and replace any special characters with spaces
		$sVariableName = (string) preg_replace('/[^a-zA-Z0-9]+/', ' ',  strtolower($sVariableName));
		// Upper case the first letter of each word and remove the spaces
		$sVariableName = (string) preg_replace('/\s+/',           null, ucwords($sVariableName));
		// Prepend the global hungarian notation
		$sVariableName = (string) "m{$sVariableName}";
		// Return the string
		return $sVariableName;
	}
}
