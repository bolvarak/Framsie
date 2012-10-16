<?php
/**
 * This class provides an easy error management system
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieError {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the C/C++ default string variable notator
	 * @var string
	 */
	const COMMON_VARIABLE_NOTATOR  = '%s';

	/**
	 * This constant contains the Framsie string variable notator
	 * @var string
	 */
	const FRAMSIE_VARIABLE_NOTATOR = ':=';

	/**
	 * This constant contains the PDO/DBI string variable notator
	 * @var string
	 */
	const PDO_VARIABLE_NOTATOR     = '?';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the errors that will be used throughout the system
	 * @access protected
	 * @staticvar array
	 */
	protected static $mErrors = array(
		'FRAMBNE' => 'The block file ":=" does not exist in the blocks directory',
		'FRAMCAC' => 'The defined cache directory ":=" does not exist',
		'FRAMCNE' => 'The controller ":=" does not exist and the default controller could not be found',
		'FRAMCNF' => 'The class ":=" could not be found',
		'FRAMDPI' => 'The DataProvider must be an array or an object.',
		'FRAMDQF' => 'The database query has failed, please check your interface setup.',
		'FRAMFMI' => 'The file or directory ":=" either does not exist or has been mistyped.',
		'FRAMFNE' => 'The form field ":=" does not exist in this form.',
		'FRAMINF' => 'Unable to instantiate the class ":="',
		'FRAMIPN' => ':= is an invalid property name, you must use "Section.Property"',
		'FRAMIRQ' => 'The request URI ":=" has been deemed invalid by the system',
		'FRAMNTM' => 'No FramsieTableMapper was set for the table\'s records, you must set this in order for FramsieTableLoader to work properly.',
		'FRAMNTS' => 'No database table was set, you must set a table before a query can be executed.',
		'FRAMNUC' => 'No unique identifier column was set, you must set this so that the system may autoload the table into the instance.',
		'FRAMPNE' => ':= does not exist in the configuration',
		'FRAMTFR' => 'There are not enough replacements for the error you requested.',
		'FRAMTMR' => 'There are too many replacements for the error you requested.',
		'FRAMUNK' => 'An unknown error has occurred',
		'FRAMURB' => 'IMagick was unable to read the image blob file.',
		'FRAMURI' => 'IMagick was unable to resize the image to ":=x:=".',
		'FRAMUWI' => 'IMagick was unable to write the image ":=".',
		'FRAMVDE' => '"The block view action ":=" does not exist in the controller ":="',
	);

	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method initializes this object with errors stored in a database table
	 * @package Framsie
	 * @subpackage FramsieError
	 * @access public
	 * @static
	 * @param string $sTable
	 * @param string $sCodeColumn
	 * @param string $sMessageColumn
	 */
	public static function InitializeErrorsFromDatabase($sTable, $sCodeColumn, $sMessageColumn) {
		// Setup the DBI
		FramsieDatabaseInterface::getInstance(true)           // Instantiate the interface
			->setTable($sTable)                               // Set the table
			->setQuery(FramsieDatabaseInterface::SELECTQUERY) // We want a SELECT query
			->addField($sCodeColumn)                          // Set the code column
			->addField($sMessageColumn)                       // Set the message column
			->generateQuery();                                // Generate the query
		// Loop through the results
		foreach (FramsieDatabaseInterface::getInstance()->getRows(PDO::FETCH_OBJ) as $oError) {
			// Add the error into the system
			self::$mErrors[$oError->{$sCodeColumn}] = (string) $oError->{$sMessageColumn};
		}
	}

	/**
	 * This method loads an error message that is stored in the system
	 * @package Framsie
	 * @subpackage FramsieError
	 * @access public
	 * @static
	 * @param multitype $mCode
	 * @param array $aReplacements
	 * @param string $sVariableNotator
	 * @return string
	 */
	public static function Load($mCode, $aReplacements = array(), $sVariableNotator = self::FRAMSIE_VARIABLE_NOTATOR) {
		// Check to see if the error exists
		if (empty(self::$mErrors[$mCode]) === false) {
			// Localize the error
			$sError       = (string) self::$mErrors[$mCode];
			// Grab the number of occurrences of the notator
			$iOccurrences = substr_count($sError, $sVariableNotator);
			// Check the number of replacements for too many
			if (count($aReplacements) < $iOccurrences) {
				// Trigger an exception
				self::Trigger('FRAMTMR');
			}
			// Check the number of replacements for too few
			if (count($aReplacements) > $iOccurrences) {
				// Trigger an exception
				self::Trigger('FRAMTFR');
			}
			// Loop through the occurrences
			for ($iOccurrence = 0; $iOccurrence < $iOccurrences; $iOccurrence++) {
				// Make the replacement
				$sError = (string) substr_replace($sError, $aReplacements[0], strpos($sError, $sVariableNotator), strlen($sVariableNotator));
				// Remove the this replacement
				array_shift($aReplacements);
			}
			// Return the proper error message
			return $sError.' (Error:  '.$mCode.')';
		}
		// Elsewise return the default error
		return self::$mErrors['FRAMUNK'].' (Error:  FRAMUNK)';
	}

	/**
	 * This method saves error messages into the system
	 * @package Framsie
	 * @subpackage FramsieError
	 * @access public
	 * @static
	 * @param multitype $mCode
	 * @param string $sMessage
	 */
	public static function Save($mCode, $sMessage) {
		// Save the error into the system
		self::$mErrors[$mCode] = (string) $sMessage;
	}

	/**
	 * This method triggers an exception with the desired error
	 * @package Framsie
	 * @subpackage FramsieError
	 * @access public
	 * @static
	 * @param multitype $mCode
	 * @param array $aReplacements
	 * @param string $sVariableNotator
	 * @throws FramsieException
	 */
	public static function Trigger($mCode, $aReplacements = array(), $sVariableNotator = self::FRAMSIE_VARIABLE_NOTATOR) {
		// Check to see if the error exists as we want it
		if (empty(self::$mErrors[$mCode])) {
			// Throw an unknown error exception
			throw new FramsieException(self::Load('FRAMUNK'), 'FRAMUNK');
		}
		// Elsewise throw the error exception we would like
		throw new FramsieException(self::Load($mCode, $aReplacements, $sVariableNotator), $mCode);
	}
}
