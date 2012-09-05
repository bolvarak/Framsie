<?php
/**
 * This class sets up the basics of a mapper class
 * @package Framsie
 * @subpackage FramsieMapper
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
abstract class FramsieMapper {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

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
	 * The constructor provides the ability to set the table name and primary
	 * key upon instantiation, but it is not required and the instance is returned
	 * @package Framsie
	 * @subpackage FramsieMapper
	 * @access public
	 * @param string [$sTableName]
	 * @param string [$sPrimaryKey]
	 * @return FramsieMapper $this
	 */
	public function __construct($sTableName = null, $sPrimaryKey = null) {
		// Check for a table name
		if (empty($sTableName) === false) {
			// Set the table name
			$this->mDbTable = (string) $sTableName;
		}
		// Check for a primary key
		if (empty($sPrimaryKey) === false) {
			// Set the primary key
			$this->mPrimaryKey = (string) $sPrimaryKey;
		}
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method ensures that a database table name is set before
	 * procedures are executed
	 * @package Framsie
	 * @subpackage FramsieMapper
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
	 * @subpackage FramsieMapper
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

	///////////////////////////////////////////////////////////////////////////
	/// Magic Methods ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method magically creates getters and setters to get and set properties
	 * and throws an exception if the method does not exist or is not publically
	 * accessible to the caller
	 * @package Framsie
	 * @subpackage FramsieMapper
	 * @access public
	 * @param string $sMethod
	 * @param multitype $sValue
	 * @throws Exception
	 * @return multitype
	 */
	public function __call($sMethod, $sValue) {
		// Check to see if this is a getter
		if (strpos($sMethod, 'get') !== false) {
			// Set the property name
			$sProperty = (string) lcfirst(str_replace('get', null, $sMethod));
			// Return the property
			return $this->{$sProperty};
		}
		// Check to see if this is a setter
		if (strpos($sMethod, 'set') !== false) {
			// Set the property name
			$sProperty = (string) ucwords(str_replace('set', null, $sMethod));
			// Set the property
			$this->{$sProperty} = $sValue;
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
	 * @subpackage FramsieMapper
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
	 * @subpackage FramsieMapper
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
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method maps a database table to the mapper
	 * @package Framsie
	 * @subpackage FramieMapper
	 * @access public
	 * @param number $iUniqueIdentifier
	 * @return FramsieMapper $this
	 */
	public function mapTable(number $iUniqueIdentifier) {
		// Check for a database table
		$this->verifyDbTable();
		// Check for a primary key
		$this->verifyPrimaryKey();
		// Setup the database interface
		FramsieDatabaseInterface::getInstance()                      // Instantiate the interface
			->setQuery(FramsieDatabaseInterface::SELECTQUERY)        // We want a SELECT query
			->setTable($this->mDbTable)                              // Set the table
			->addField(FramsieDatabaseInterface::WILDCARD)           // SELECT *
			->addWhereClause($this->mPrimaryKey, $iUniqueIdentifier) // Send the ID
			->generateQuery();                                       // Build the query
		// Grab the row
		$oMapRow = (object) FramsieDatabaseInterface::getInstance()->getRow(PDO::FETCH_OBJ);
		// Loop through the object
		foreach ($oMapRow as $sColumn => $sValue) {
			// Define the method name
			$sMethod = (string) 'set'.ucwords($sColumn);
			// Set the property
			$this->{$sMethod}($sValue);
		}
		// Return the instance
		return $this;
	}


	public function saveObject() {

	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current database table name
	 * @package Framsie
	 * @subpackage FramsieMapper
	 * @access public
	 * @return string
	 */
	public function getDbTable() {
		// Return the current database table
		return $this->mDbTable;
	}

	/**
	 * This method returns the current primary key column name
	 * @package Framsie
	 * @subpackage FramsieMapper
	 * @access public
	 * @return string
	 */
	public function getPrimaryKey() {
		// Return the current primary key field
		return $this->mPrimaryKey;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the database table name into the system
	 * @package Framsie
	 * @subpackage FramsieMapper
	 * @access public
	 * @param string $sTableName
	 * @return FramsieMapper $this
	 */
	public function setDbTable($sTableName) {
		// Set the database table name into the system
		$this->mDbTable = (string) $sTableName;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the primary key column name into the system
	 * @package Framsie
	 * @subpackage FramsieMapper
	 * @access public
	 * @param string $sColumn
	 * @return FramsieMapper $this
	 */
	public function setPrimaryKey($sColumn) {
		// Set the primary key into the system
		$this->mPrimaryKey = (string) $sColumn;
		// Return the instance
		return $this;
	}
}
