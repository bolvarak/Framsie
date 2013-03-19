<?php
/**
 * This class provides an easy string manipulation interface
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieString {
	
	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This property contains all of the special characters and their replacements
	 * @access protected
	 * @staticvar array
	 */
	protected static $mSpecialCharacters = array(
		'&acirc;€“'                => '-',
		'&acirc;€œ'                => '&ldquo;',
		'&acirc;€˜'                => '&lsquo;',
		'&acirc;€™'                => '&rsquo;',
		'&Acirc;&pound;'           => '&pound;',
		'&Acirc;&not;'             => '&not;',
		'&acirc;„&cent;'           => '&#8482;',
		'&acirc;€'                 => '&rdquo;',
		"&#39;"                    => "'", 
		"\xc3\xa2\xc2\x80\xc2\x99" => "'", 
		"\xc3\xa2\xc2\x80\xc2\x93" => ' - ', 
		"\xc3\xa2\xc2\x80\xc2\x9d" => '"', 
		"\xc3\xa2\x3f\x3f"         => "'",
		"\xC2\xAB"                 => '"',
		"\xC2\xBB"                 => '"',
		"\xE2\x80\x98"             => "'",
		"\xE2\x80\x99"             => "'",
		"\xE2\x80\x9A"             => "'",
		"\xE2\x80\x9B"             => "'",
		"\xE2\x80\x9C"             => '"',
		"\xE2\x80\x9D"             => '"',
		"\xE2\x80\x9E"             => '"',
		"\xE2\x80\x9F"             => '"',
		"\xE2\x80\xB9"             => "'",
		"\xE2\x80\xBA"             => "'",
		"\xe2\x80\x93"             => "-",
		"\xc2\xb0"	           => "°",
		"\xc2\xba"                 => "°",
		"\xc3\xb1"	           => "&#241;",
		"\x96"		           => "&#241;",
		"\xe2\x81\x83"             => '&bull;',
		"\xd5"                     => "'"
	);
	
	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method cleanses a string of the blasphemy that is MS Word encoding
	 * @package Framsie
	 * @subpackage FramsieString
	 * @access public
	 * @static
	 * @param string $sString
	 * @return string
	 */
	public static function Cleanse($sString) {
		// Return the cleansed string
		return strtr($sString, self::$mSpecialCharacters);
	}
}
