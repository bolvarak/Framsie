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
	 * This method loads a configuration value from the database
	 * @package Framsie
	 * @subpackage FramsieConfiguration
	 * @access public
	 * @static
	 * @param string $sProperty
	 * @throws FramsieException
	 * @return multitype
	 */
	public static function DbLoad($sProperty) {
		// Check for a proper section and property name
		if (strpos($sProperty, '.') === false) {
			// Throw an exception
			FramsieError::Trigger('FRAMIPN', array($sProperty));
		}
		// Split the property name from the section name
		$aProperty = explode('.', $sProperty);
		// Setup the interface
		FramsieDatabaseInterface::getInstance(true)                                                  // Instantiate the interface
			->setTable      ('ConfigProperties')                                                     // Send the table
			->setQuery      (FramsieDatabaseInterface::SELECTQUERY)                                  // We want a SELECT query
			->addInnerJoin  ('ConfigSections', 'SectionId',                      'SectionId')        // Add the INNER JOIN
			->addField      ('Value',          null,                             'ConfigProperties') // Add the field we want
			->addWhereClause('Name',           $aProperty[0],                    'ConfigSections')   // Add the section name to the WHERE clause
			->addWhereClause('Key',            $aProperty[1],                    'ConfigProperties') // Add the property name to the WHERE clause
			->generateQuery ();                                                                      // Generate the query
		// Grab the row
		$oRow      = FramsieDatabaseInterface::getInstance()->getRow(PDO::FETCH_OBJ);
		// Check for a value
		if (empty($oRow->Value) && (isset($oRow->Value) === false)) {
			// Throw an exception
			FramsieError::Trigger('FRAMPNE', array($sProperty));
		}
		// Return the value
		return $oRow->Value;
	}

	/**
	 * This method loads the configuration or specific parts of the configuration
	 * @package FramsieConfiguration
	 * @access public
	 * @static
	 * @param string [$sProperty]
	 * @throws FRamsieException
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
				FramsieError::Trigger('FRAMPNE', array($sProperty));
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
				FramsieError::Trigger('FRAMPNE', array($sPropertyKey));
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
