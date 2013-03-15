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
class FramsieTableMapper {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the pattern that matches a boolean type column
	 * @var string
	 */
	const PATTERN_BOOLEAN = '/(tinyint\(1\)|bool)/i';

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
	protected $mColumns       = array();

	/**
	 * This property contains the database table name associated with this map
	 * @access protected
	 * @var string
	*/
	protected $mDbTable       = null;

	/**
	 * This property tells the system whether or not this is a lookup table and thus to ignore the primary key
	 * @access protected
	 * @var boolean
	 */
	protected $mIsLookupTable = false;

	/**
	 * This property contains the database table's primary key column
	 * associated with this map
	 * @access protected
	 * @var string
	 */
	protected $mPrimaryKey    = null;

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
		$this->mDbTable    = (string) $sTableName;
		// Set the primary key column name
		$this->mPrimaryKey = (string) $sPrimaryKey;
		// Load the columns and properties
		$this->initializeObject();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Magic Methods ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

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
		if (substr(strtolower($sMethod), 0, 3) == 'get') {
			// Set the property name
			$sColumn = (string) substr_replace($sMethod, null, 0, 3);
			// Make sure the column exists
			if (array_key_exists($sColumn, $this->mColumns) === false) {
				// Throw an exception
				throw new Exception("The column \"{$sColumn}\" does not exist in the table \"{$this->mDbTable}\".");
			}
			// Return the property
			return $this->{'m'.$sColumn};
		}
		// Check to see if this is a setter
		if (substr(strtolower($sMethod), 0, 3) == 'set') {
			// Set the property name
			$sColumn = (string) substr_replace($sMethod, null, 0, 3);
			// Make sure the column exists
			if (array_key_exists($sColumn, $this->mColumns) === false) {
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
		// Make sure the column exists
		if (array_key_exists(substr($sProperty, 1), $this->mColumns) === false) {
			// Throw an exception
			throw new Exception("The column \"".substr($sProperty, 1)."\" does not exist in the table \"{$this->mDbTable}\" or is not readable.");
		}
		// Return the property
		return $this->{$sProperty};
	}

	/**
	 * This method throws an exception if a property does not exist or is not
	 * publically accessible to the caller
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @param string $sProperty
	 * @param multitype $mValue
	 * @throws Exception
	 * @return void
	 */
	public function __set($sProperty, $mValue) {
		// Make sure the column exists
		if (array_key_exists(substr($sProperty, 1), $this->mColumns) === false) {
			// Throw an exception
			throw new Exception("The column \"".substr($sProperty, 1)."\" does not exist in the table \"{$this->mDbTable}\" or is not writable.");
		}
		// Set the property
		$this->{$sProperty} = $this->determineValueType(substr($sProperty, 1), $mValue);
		// Return the instance
		return $this;
	}
	
	/**
	 * This method converts the mapper to a JavaScript object with functions
	 * @package Framsie
	 * @subpackage FramsieMapper
	 * @access public
	 * @return string
	 */
	public function __toJavascript() {
		// Start the object string off
		$sJavascript = (string) '{';
		// Loop through the properties
		foreach (get_object_vars($this) as $sName => $mValue) {
			// Add the property to the json string
			$sJavascript .= (string) "\"{$sName}\": ".json_encode($mValue).", ";
		}
		// Loop through the properties once more
		foreach (get_object_vars($this) as $sName => $mValue) {
			// Add the getter to the json string
			$sJavascript .= (string) "\"get".substr($sName, 1)."\": function() { return this['{$sName}']; }, ";
		}
		// Loop through the final time
		foreach (get_object_vars($this) as $sName => $mValue) {
			// Add the setter to the json string
			$sJavascript .= (string) "\"set".substr($sName, 1)."\": function(mValue) { this['{$sName}'] = mValue; return this; }, ";
		}
		// Trim the commas
		$sJavascript  = (string) rtrim($sJavascript, ',');
		// Finish the string off
		$sJavascript .= (string) '}';
		// Return the JSONP string
		return $sJavascript;
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
				case 'CURRENT_TIMESTAMP' : 
					// Check for a set timezone
					if (ini_get('date.timezone')) {
						// Create the datetime object
						$oDate = new DateTime('now', new DateTimeZone(ini_get('date.timezone')));
						// Set the default value
						$sDefaultValue = $oDate->format('Y-m-d H:i:s');
					} else {
						// Use the system default
						$sDefaultValue = date('Y-m-d H:i:s');
					}
				// We're done
				break;
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
		if (preg_match(self::PATTERN_BOOLEAN, $this->mColumns[$sColumn]->Type) && (($mValue == 1) || ($mValue == 0))) { // Boolean
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
			// Check to see if this is a DateTime object
			if ($oDate = new DateTime($mValue)) {
				// Check for a timezone
				if (ini_get('date.timezone')) {
					// Set the timezone
					$oDate->setTimezone(new DateTimezone(ini_get('date.timezone')));
				}
				// Return the date value
				return (string) $oDate->format('Y-m-d H:i:s');
			}
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
		$this->mColumns = new FramsieDatabaseInterface();
		$this->mColumns = $this->mColumns
                        ->setTable  ($this->mDbTable) // Send the table name
			->getColumns();               // Load the column data

		// Loop through the columns
		foreach ($this->mColumns as $sColumn => $oColumn) {
			// Set the property
			$this->{"m{$sColumn}"} = $this->determineDefaultValueType($oColumn);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method loops through the column to property map and adds the fields
	 * and values to the database interface
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @param boolean $bInsertUpdate
	 * @return FramsieTableMapper $this
	 */
	protected function setColumnMapIntoInterface($bInsertUpdate = false) {
		// Check to see if this is an INSERT or UPDATE query
		if ($bInsertUpdate === true) {
			// Loop through the columns and add the fields
			foreach ($this->mColumns as $sColumn => $oColumn) {
				// Make sure this column isn't the primary key and that the property has a value
				if (($sColumn !== $this->mPrimaryKey) || (($this->mIsLookupTable === true) && (empty($this->{'m'.$sColumn}) === false))) {
					// Add the field to the interface
					FramsieDatabaseInterface::getInstance()->addField($sColumn, $this->{'m'.$sColumn});
				}
			}
			// Return the instance
			return $this;
		}
		// Loop through the columns and add the fields
		foreach ($this->mColumns as $sColumn => $oColumn) {
			// Make sure this column isnt't the primary key
			if ($sColumn !== $this->mPrimaryKey) {
				// Add the field
				FramsieDatabaseInterface::getInstance()->addField($sColumn);
			}
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
		if (empty($this->mPrimaryKey) && ($this->mIsLookupTable === false)) {
			// Throw an exception because the caller needs a primay key column
			throw new Exception('A primary key column name is needed and not set.');
		}
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method deletes the record stored in this object from the database
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access public
	 * @param array $aWhere
	 * @return boolean
	 */
	public function delete($aWhere = array()) {
		// Verify the DB table
		$this->verifyDbTable();
		// Verify the primary
		$this->verifyPrimaryKey();
		// Make sure we have a primary key
		if (empty($this->{'m'.$this->mPrimaryKey})) {
			// Throw a new exception
			FramsieError::Trigger('FRAMNPK');
		}
		// Setup the DBI
		FramsieDatabaseInterface::getInstance(true)
			->setTable($this->mDbTable)
			->setQuery(FramsieDatabaseInterface::DELETEQUERY)
			->addWhereClause($this->mPrimaryKey, $this->{'m'.$this->mPrimaryKey});
		// Loop through the other WHERE clauses
		foreach ($aWhere as $sColumn => $mValue) {
			// Add the WHERE clause
			FramsieDatabaseInterface::getInstance()->addWhereClause($sColumn, $mValue);
		}
		// Generate the query
		FramsieDatabaseInterface::getInstance()->generateQuery();
		// Return the execution status
		return FramsieDatabaseInterface::getInstance()->getQueryExecutionStatus();
	}

	/**
	 * This method sets up the mapper from a PDO result
	 * @package Framsie
	 * @subpackage FramsieMapper
	 * @access public
	 * @param object|array $oPdoResults
	 * @return FramsieMapper $this
	 */
	public function fromPdo($oPdoResults) {
		// Load the PDO result set into the object
		$this->initializeObject();
                // Loop through the PDO object if one is provided
        	foreach ($oPdoResults as $sColumn => $mValue) {
			// Check to see if the column exists
			if (array_key_exists($sColumn. $this->mColumns)) {
				// Set the property
				$this->{"m{$sColumn}"} = $this->determineValueType($sColumn, $mValue);
			}
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method maps a database table to the mapper
	 * @package Framsie
	 * @subpackage FramieTableMapper
	 * @access public
	 * @throws Exception
	 * @param integer $iUniqueIdentifier
	 * @param array $aWhere
	 * @return FramsieMapper $this
	 */
	public function load($iUniqueIdentifier = null, $aWhere = array()) {
		// Check for a database table
		$this->verifyDbTable();
		// Check for a primary key
		$this->verifyPrimaryKey();
		// Check for to see if this is a lookup table
		if ($this->mIsLookupTable === false) {
			// Set the primary key
			$this->{'m'.$this->mPrimaryKey} = (integer) $iUniqueIdentifier;
		}
		// Setup the database interface
		FramsieDatabaseInterface::getInstance(true)       // Instantiate the interface
		        ->setQuery(FramsieDatabaseInterface::SELECTQUERY) // We want a SELECT query
		        ->setTable($this->mDbTable);                      // Set the table
		// Check to see if we are simply loading by ID
		if (empty($iUniqueIdentifier) && empty($aWhere)) {
			// Throw an exception
			throw new Exception('You must provide either a primary key unique identifier or at least one additional WHERE clause.');
		}
		// Check to see if the unique identifier is empty
		if (is_null($iUniqueIdentifier) === false) {
			// Set the unique ID
			FramsieDatabaseInterface::getInstance()->addWhereClause($this->mPrimaryKey, $iUniqueIdentifier);
		}
		// Loop through the additional WHERE clauses
		foreach ($aWhere as $sColumn => $mValue) {
			// Add the WHERE clause
			FramsieDatabaseInterface::getInstance()->addWhereClause($sColumn, $mValue);
		}
		// Add the fields to the interface
		$this->setColumnMapIntoInterface(false);
		// Generate the query
		FramsieDatabaseInterface::getInstance()->generateQuery();
		// Grab the row
		$oMapRow = (object) FramsieDatabaseInterface::getInstance()->getRow(PDO::FETCH_OBJ);
		// Check for a row
		if (empty($oMapRow)) {
			// We're done
			return false;
		}
		// Loop through the object
		foreach ($oMapRow as $sColumn => $mValue) {
			// Set the property
			$this->{"m{$sColumn}"} = $this->determineValueType($sColumn, $mValue);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method saves the instance of this class back into the database
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access public
	 * @throws Exception
	 * @param array $aWhere
	 * @return FramsieMapper $this
	 */
	public function save($aWhere = array()) {
		// Check for a database table
		$this->verifyDbTable();
		// Check for a primary key
		$this->verifyPrimaryKey();
		// Check to see if we have a primary key value
		if (empty($this->{'m'.$this->mPrimaryKey}) || ($this->mIsLookupTable === true)) { // Run an INSERT
			// Setup the database interface
			FramsieDatabaseInterface::getInstance(true)               // Instantiate the interface
			        ->setQuery(FramsieDatabaseInterface::INSERTQUERY) // We want an INSERT query
			        ->setTable($this->mDbTable);                      // Set the table
			// Add the fields to the insterface
			$this->setColumnMapIntoInterface(true);
			// Loop through the where clause
			foreach ($aWhere as $sColumn => $mValue) {
				// Add the where clause
				FramsieDatabaseInterface::getInstance()->addWhereClause($sColumn, $mValue);
			}
			// Generate the query
			FramsieDatabaseInterface::getInstance()->generateQuery();
			// Execute the statement
			if (!FramsieDatabaseInterface::getInstance()->getQueryExecutionStatus()) {
				// Throw an exception
				throw new Exception('Could not insert instance of '.get_class($this)." into database table {$this->mDbTable}.");
			}
			// Check to see if this is a lookup table
			if ($this->mIsLookupTable === false) {
				// Set the global name
				$sGlobal = (string) "m{$this->mPrimaryKey}";
				// Set the primary key into the object
				$this->{$sGlobal} = (integer) FramsieDatabaseInterface::getInstance()->getLastInsertId();
			}
			// Return the instane
			return $this;
		}
		// We're running an UPDATE query
		FramsieDatabaseInterface::getInstance(true)           // Instantiate the interface
			->setQuery(FramsieDatabaseInterface::UPDATEQUERY)     // We want an UPDATE query
			->setTable($this->mDbTable);                          // Set the table
		// Add the fields to the interface
		$this->setColumnMapIntoInterface(true);
		// Add the WHERE clause
		FramsieDatabaseInterface::getInstance()
			->addWhereClause($this->mPrimaryKey, $this->{'m'.$this->mPrimaryKey});
		// Loop through the where clauses
		foreach ($aWhere as $sColumn => $mValue) {
			// Add the where clause
			FramsieDatabaseInterface::getInstance()->addWhereClause($sColumn, $mValue);
		}
		// Generate the query
		FramsieDatabaseInterface::getInstance()->generateQuery();
		// Execute the statement
		if (!FramsieDatabaseInterface::getInstance()->getQueryExecutionStatus()) {
			// Throw an exception
			throw new Exception('Could not update instance of '.get_class($this)." into database table {$this->mDbTable}.");
		}
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current lookup table identifier
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access public
	 * @return boolean
	 */
	public function getIsLookupTable() {
		// Return the current lookup table identifier
		return $this->mIsLookupTable;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the lookup table identifier into the system
	 * @package Framsie
	 * @subpackage FramsieLookupTable
	 * @access public
	 * @param boolean $bIsLookupTable
	 * @return FramsieTableMapper $this
	 */
	public function setIsLookupTable($bIsLookupTable) {
		// Set the lookup table identifier
		$this->mIsLookupTable = (boolean) $bIsLookupTable;
		// Return the instance
		return $this;
	}
}
