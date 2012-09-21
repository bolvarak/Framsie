<?php
/**
 * This class sets up the automatic database table mapper
 * @package Framsie
 * @subpackage FramsieTableMapper
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
abstract class FramsieTableMapper {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the pattern that matches a boolean type column
	 * @var string
	 */
	const PATTERN_BOOLEAN = '/(tinyint|bool)/i';

	/**
	 * This constant contains the pattern that matches a float type column
	 * @var string
	 */
	const PATTERN_FLOAT   = '/(float|decimal)/i';

	/**
	 * This constant contains the pattern that matches an integer type column
	 * @var string
	 */
	const PATTERN_INTEGER = '/(?!tinyint)(int)/i';

	/**
	 * This constant contains the pattern that matches a string type column
	 * @var string
	 */
	const PATTERN_STRING  = '/(char|text|blob|date|timestamp|datetime|time|unix_timestamp)/i';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the column map
	 * @access protected
	 * @var array
	 */
	protected $mColumns    = array();

	/**
	 * This property contains the database table name associated with this map
	 * @access protected
	 * @var string
	*/
	protected $mDbTable    = null;

	/**
	 * This property contains the database table's primary key column
	 * associated with this map
	 * @access protected
	 * @var string
	 */
	protected $mPrimaryKey = null;

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor
	 * @param string $sTableName
	 * @param string $sPrimaryKey
	 * @return FramsieTableMapper $this
	 */
	public function __construct($sTableName, $sPrimaryKey) {
		// Set the table name
		$this->mDbTable = (string) $sTableName;
		// Set the primary key column name
		$this->mPrimaryKey = (string) $sPrimaryKey;
		// Load the columns and properties
		$this->initializeObject();
		// Return the instance
		return $this;
	}

	/**
	 * This method magically creates getters and setters to get and set properties
	 * and throws an exception if the method does not exist or is not publically
	 * accessible to the caller
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access public
	 * @param string $sMethod
	 * @param array $aValue
	 * @throws Exception
	 * @return multitype
	 */
	public function __call($sMethod, $aArguments) {
		// Check to see if this is a getter
		if (substr(strtolower($sMethod), 0, 3) === 'get') {
			// Set the property name
			$sColumn = (string) preg_replace('/get/i', null, $sMethod);
			// Make sure the column exists
			if (empty($this->mColumns[$sColumn])) {
				// Throw an exception
				throw new Exception("The column \"{$sColumn}\" does not exist in the table \"{$this->mDbTable}\".");
			}
			// Return the property
			return $this->{'m'.$sColumn};
		}
		// Check to see if this is a setter
		if (substr(strtolower($sMethod), 0, 3) === 'set') {
			// Set the property name
			$sColumn = (string) preg_replace('/set/i', null, $sMethod);
			// Make sure the column exists
			if (empty($this->mColumns[$sColumn])) {
				// Throw an exception
				throw new Exception("The column \"{$sColumn}\" does not exist in the table \"{$this->mDbTable}\".");
			}
			// Set the property
			$this->{'m'.$sColumn} = $this->determineValueType($sColumn, $aArguments[0]);
			// Return the instance
			return $this;
		}
		// If the script gets to this point, throw an exception
		throw new Exception("The method \"{$sMethod}\" does not exist or is not publically accessible.");
	}

	/**
	 * This method throws an exception if a property does not exist or is not
	 * publically accessible to the caller
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access public
	 * @param string $sProperty
	 * @throws Exception
	 * @return void
	 */
	public function __get($sProperty) {
		// Throw an exception because the property does not exist
		throw new Exception("The property \"{$sProperty}\" does not exist or is not publically accessible.");
	}

	/**
	 * This method throws an exception if a property does not exist or is not
	 * publically accessible to the caller
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @param string $sProperty
	 * @param multitype $sValue
	 * @throws Exception
	 * @return void
	 */
	public function __set($sProperty, $sValue) {
		// Throw an exception because the property does not exist
		throw new Exception("The property \"{$sProperty}\" does not exist or is not publically accessible.");
	}

	/**
	 * This method converts the this instance to a string and into JSON
	 * to make it usable across different languages
	 * @package Framsie
	 * @subpackage FramsieMapper
	 * @access public
	 * @return string
	 */
	public function __toString() {
		// Convert the class to JSON
		return json_encode($this);
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method determines the actual default variable type and default value
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access protected
	 * @param object $oColumn
	 * @return boolean|float|integer|string
	 */
	protected function determineDefaultValueType($oColumn) {
		// Create a default value placeholder
		$sDefaultValue = null;
		// Determine the default value
		if (empty($oColumn->Default) === false) {
			// Determine if the default value should be anything special
			switch ($oColumn->Default) {
				// Timestamp
				case 'CURRENT_TIMESTAMP' : $sDefaultValue = date('Y-m-d H:i:s'); break;
				// Everything else
				default                  : $sDefaultValue = $oColumn->Default;   break;
			}
		}
		// Determine the data type
		if (preg_match(self::PATTERN_BOOLEAN, $oColumn->Type)) { // Boolean
			// Return the value
			return (boolean) $sDefaultValue;
		}
		if (preg_match(self::PATTERN_FLOAT,   $oColumn->Type)) { // Float
			// Return the value
			return (float)   $sDefaultValue;
		}
		if (preg_match(self::PATTERN_INTEGER, $oColumn->Type)) { // Integer
			// Return the value
			return (integer) $sDefaultValue;
		}
		if (preg_match(self::PATTERN_STRING,  $oColumn->Type)) { // String
			// Return the value
			return (string)  $sDefaultValue;
		}
		// Return a null type value
		return $sDefaultValue;
	}

	/**
	 * This method determines the actual column variable type and returns the
	 * value as the property type
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access protected
	 * @param string $sColumn
	 * @param multitype $mValue
	 * @return boolean|float|integer|string
	 */
	protected function determineValueType($sColumn, $mValue) {
		// Determine the data type
		if (preg_match(self::PATTERN_BOOLEAN, $this->mColumns[$sColumn]->Type)) { // Boolean
			// Return the value
			return (boolean) $mValue;
		}
		if (preg_match(self::PATTERN_FLOAT,   $this->mColumns[$sColumn]->Type)) { // Float
			// Return the value
			return (float)   $mValue;
		}
		if (preg_match(self::PATTERN_INTEGER, $this->mColumns[$sColumn]->Type)) { // Integer
			// Return the value
			return (integer) $mValue;
		}
		if (preg_match(self::PATTERN_STRING,  $this->mColumns[$sColumn]->Type)) { // String
			// Return the value
			return (string)  $mValue;
		}
		// Return a null type value
		return $mValue;
	}

	/**
	 * This method loads the columns and their meta data from the database
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access protected
	 * @return FramsieTableMapper $this
	 */
	protected function initializeObject() {
		// Make sure the table name is set
		$this->verifyDbTable();
		// Load the column data
		$this->mColumns = FramsieDatabaseInterface::getInstance(true)
			->setTable  ($this->mDbTable) // Send the table name
			->getColumns();               // Load the column data
		// Loop through the columns
		foreach ($this->mColumns as $sColumn => $oColumn) {
			// Set the property
			$this->{'m'.$sColumn} = $this->determineDefaultValueType($oColumn);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method ensures that a database table name is set before
	 * procedures are executed
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access protected
	 * @throws Exception
	 * @return void
	 */
	protected function verifyDbTable() {
		// Check for a database table
		if (empty($this->mDbTable)) {
			// Throw an exception because the caller needs a db table name
			throw new Exception('A database table name is needed and not set.');
		}
	}

	/**
	 * This method ensures that a primary key column name is set before
	 * procedures are executed
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access protected
	 * @throws Exception
	 * @return void
	 */
	protected function verifyPrimaryKey() {
		// Check for a primary key column name
		if (empty($this->mPrimaryKey)) {
			// Throw an exception because the caller needs a primay key column
			throw new Exception('A primary key column name is needed and not set.');
		}
	}
}
