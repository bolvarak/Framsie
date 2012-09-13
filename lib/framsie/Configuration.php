<?php
/**
 * This class provides an easy configuration file access
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieConfiguration {

	///////////////////////////////////////////////////////////////////////////
	/// Proeprties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property holds the configuration file data
	 * @access protected
	 * @staticvar
	 * @var array
	 */
	protected static $mConfiguration = array();

	///////////////////////////////////////////////////////////////////////////
	/// Protected Static Methods /////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method loads or reloads the configuration file into the system
	 * @package FramsieConfiguration
	 * @access protected
	 * @static
	 * @param boolean [$bReload]
	 * @return void
	 */
	protected static function EnsureConfigurationIsLoaded($bReload = false) {
		// Check for an empty configuration
		if (empty(self::$mConfiguration) || ($bReload === true)) {
			// Read the configuration file
			self::$mConfiguration = parse_ini_file(CONFIGURATION_FILE_PATH, true);
		}
		// We're done
		return;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method loads the configuration or specific parts of the configuration
	 * @package FramsieConfiguration
	 * @access public
	 * @static
	 * @param string [$sProperty]
	 * @throws Exception
	 * @return multitype
	 */
	public static function Load($sProperty = null) {
		// Make sure the configuration is loaded
		self::EnsureConfigurationIsLoaded();
		// Check for an empty property key
		if (empty($sProperty)) {
			// Return the entire configuration file
			return self::$mConfiguration;
		}

		// Check for a delimiter in the property key
		if (strpos($sProperty, '.') === false) {
			// Check for the configuration value
			if (empty(self::$mConfiguration[$sProperty])) {
				// Throw an exception
				throw new Exception("The configuration section or property \"{$sProperty}\" does not exist.");
			}
			// Return the property
			return self::$mConfiguration[$sProperty];
		}

		// Localize the configuration
		$aConfiguration = self::$mConfiguration;
		// Separate the delimiters
		$aParts         = explode('.', $sProperty);
		// Loop through the parts
		foreach ($aParts as $sPropertyKey) {
			// Check for the existance of the property
			if (empty($aConfiguration[$sPropertyKey])) {
				// Throw an exception
				throw new Exception("The configuration section or property \"{$sPropertyKey}\" does not exist.");
			}
			// Reset the configuration
			$aConfiguration = $aConfiguration[$sPropertyKey];
		}
		// Return the requested configuration
		return $aConfiguration;
	}

	/**
	 * This method temporarily saves properties to the configuration array
	 * @pacakge FramsieConfiguration
	 * @access public
	 * @static
	 * @param string $sProperty
	 * @param multitype $sValue
	 * @return boolean
	 */
	public static function Save($sProperty, $sValue) {
		// Make sure the configuration is loaded
		self::EnsureConfigurationIsLoaded();
		// Check for a delimiter in the property key
		if (strpos($sProperty, '.') === false) {
			// Set the configuration
			self::$mConfiguration[$sProperty] = $sValue;
			// We're done
			return true;
		}
		// Separate the delimiters
		$aParts = explode('.', $sProperty);
		// Loop through the parts
		foreach ($aParts as $sPropertyKey) {
			// Set the configuration
			self::$mConfiguration[$sPropertyKey] = $sValue;
		}
		// We're done
		return true;
	}
}
