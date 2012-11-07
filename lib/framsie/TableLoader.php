<?php
/**
 * This class provides an easy database table record interface
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieTableLoader {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the name of the default TableMapper instance to use
	 * @var string
	 */
	const DEFAULT_TABLE_MAPPER         = 'FramsieTableMapper';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of the class
	 * @access protected
	 * @staticvar FramsieTableLoader
	 */
	protected static $mInstance        = null;

	/**
	 * This property contains the instance of our database interface
	 * @access protected
	 * @var FramsieDatabaseInterface
	 */
	protected $mDBI                    = null;

	/**
	 * This property contains the list of fields to load
	 * @access protected
	 * @var array
	 */
	protected $mFields                 = array();

	/**
	 * This property contains the dataset
	 * @access protected
	 * @var array<FramsieTableMapper>
	 */
	protected $mIterator               = array();

	/**
	 * This property contains ORDER BY clauses
	 * @access protected
	 * @var array
	 */
	protected $mOrderByClauses         = array();

	/**
	 * This property contains the name of the table to query
	 * @access protected
	 * @var string
	 */
	protected $mTable                  = null;

	/**
	 * This property contains the FramsieTableMapper instance to instantiate
	 * @access protected
	 * @var string
	 */
	protected $mTableMapper            = self::DEFAULT_TABLE_MAPPER;

	/**
	 * This property contains the column name that has the unique identifier
	 * @access protected
	 * @var string
	 */
	protected $mUniqueIdentifierColumn = null;

	/**
	 * This property contains a list of unique identifier values
	 * @access protected
	 * @var array
	 */
	protected $mUniqueIdentifiers      = array();

	/**
	 * This property contains the clauses for the WHERE statement
	 * @access protected
	 * @var array
	 */
	protected $mWhereClauses           = array();

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method maintains access to the singleton instance of this class
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieTableLoader self::$mInstance
	 */
	public static function getInstance($bReset = false) {
		// Determine if an instance exists or a reset notification has been sent
		if (empty(self::$mInstance) || ($bReset === true)) {
			// Create a new instance
			self::$mInstance = new self();
		}
		// Return the instance
		return self::$mInstance;
	}

	/**
	 * This method sets an external instance into this class
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @static
	 * @param FramsieTableLoader $oInstance
	 * @return FramsieTableLoader self::$mInstance
	 */
	public static function setInstance(FramsieTableLoader $oInstance) {
		// Set the external instance into the class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply returns the instance until we find something
	 * awesome to do with it
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @return FramsieTableLoader $this
	 */
	public function __construct() {
		// Create a new instance of the DBI
		$this->mDBI = FramsieDatabaseInterface::getInstance(true);
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method populates the iterator with results from the database
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access protected
	 * @return FramsieTableLoader $this
	 */
	protected function populateIterator() {
		// Loop through the rows
		foreach ($this->mDBI->getRows() as $oRow) {
			// Check for the default TableMapper
			if ($this->mTableMapper === self::DEFAULT_TABLE_MAPPER) {
				// Instantiate the TableMapper
				$oMapper = Framsie::Loader($this->mTableMapper, $this->mTable, $this->mUniqueIdentifierColumn);
				// Push the TableMapper instance
				array_push($this->mIterator, $oMapper->load($this->mUniqueIdentifierColumn));
			} else {
				// Instantiate the TableMapper
				$oMapper = Framsie::Loader($this->mTableMapper);
				array_push($this->mIterator, $oMapper->load($oRow->{$this->mUniqueIdentifierColumn}));
			}
			// Add the primary keys to the system
			array_push($this->mUniqueIdentifiers, $oRow->{$this->mUniqueIdentifierColumn});
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method sets up our instance of FramsieDatabaseInterface with all of
	 * the data it needs in order to successfully perform a query
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access protected
	 * @return FramsieTableLoader $this
	 */
	protected function setupDBI() {
		// Set the query
		$this->mDBI->setQuery(FramsieDatabaseInterface::SELECTQUERY);
		// Set the table
		$this->mDBI->setTable($this->mTable);
		// Set the field to select
		$this->mDBI->addField($this->mUniqueIdentifierColumn);
		// Loop through the ORDER BY clauses
		foreach ($this->mOrderByClauses as $sColumn => $sDirection) {
			// Add the ORDER BY clause
			$this->mDBI->addOrderBy($sColumn, $sDirection);
		}
		// Add the WHERE clauses
		foreach ($this->mWhereClauses as $sColumn => $aData) {
			// Add the WHERE clause
			$this->mDBI->addWhereClause($sColumn, $aData['mValue'], null, $aData['sOperator']);
		}
		// Generate the query
		$this->mDBI->generateQuery();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds an ORDER BY clause to the system to organize the results
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @param string $sColumn
	 * @param string $sDirection
	 * @return FramsieTableLoader $this
	 */
	public function addOrderByClause($sColumn, $sDirection = FramsieDatabaseInterface::ASCORD) {
		// Add the clause to the system
		$this->mOrderByClauses[$sColumn] = $sDirection;
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a WHERE clause to the system to limit the results
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @param string $sColumn
	 * @param multitype $mValue
	 * @param string $sOperator
	 * @return FramsieTableLoader $this
	 */
	public function addWhereClause($sColumn, $mValue, $sOperator = FramsieDatabaseInterface::EQOP) {
		// Add the clause to the system
		$this->mWhereClauses[$sColumn] = array(
			'mValue'    => $mValue,
			'sOperator' => $sOperator
		);
		// Return the instance
		return $this;
	}

	/**
	 * This method deletes all of the records loaded by the load method
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @return FramsieTableLoader $this
	 */
	public function delete() {
		// Loop through the iterator
		foreach ($this->getIterator() as $oMapper) {
			// Delete the record
			$oMapper->delete();
		}
		// We're done
		return $this;
	}

	/**
	 * This method runs the system checks and starts the process of populating
	 * the iterator with the dataset
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access public
	 * @param string $sTable
	 * @param string $sUniqueIdentifierColumn
	 * @param string $sTableMapper
	 * @param array $aWhere
	 * @throws FramsieException
	 * @return FramsieTableLoader $this
	 */
	public function load($sTable = null, $sUniqueIdentifierColumn = null, $sTableMapper = null, $aWhere = array()) {
		// Make sure the table is set
		if (empty($this->mTable) && empty($sTable)) {
			// Trigger an exception
			FramsieError::Trigger('FRAMNTS');
		}
		// Make sure the unique ID column is set
		if (empty($this->mUniqueIdentifierColumn) && empty($sUniqueIdentifierColumn)) {
			// Trigger an exception
			FramsieError::Trigger('FRAMNUC');
		}
		// Make sure the TableMapper class name is set
		if (empty($this->mTableMapper) && empty($sTableMapper)) {
			// Trigger an exception
			FramsieError::Trigger('FRAMNTM');
		}
		// Check to see if the caller used a fluid call
		if (empty($this->mTable)) {                  // Table
			// Set the table
			$this->mTable = (string) $sTable;
		}
		if (empty($this->mUniqueIdentifierColumn)) { // Unique ID column
			// Set the unique ID column name
			$this->mUniqueIdentifierColumn = (string) $sUniqueIdentifierColumn;
		}
		if (empty($this->mTableMapper)) {            // Table Mapper Class Name
			// Set the table mapper class name
			$this->mTableMapper = (string) $sTableMapper;
		}
		if (empty($this->mWhereClauses)) {           // WHERE clauses
			// Set the WHERE clauses
			$this->mWhereClauses = $aWhere;
		}
		// Setup the DBI
		$this->setupDBI();
		// Populate the iterator
		$this->populateIterator();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current FramsieDatabaseInterface instance
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @return FramsieDatabaseInterface
	 */
	public function getDBI() {
		// Return the current DBI
		return $this->mDBI;
	}

	/**
	 * This method returns the current list of fields to return
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @return array
	 */
	public function getFields() {
		// Return the current fieldset
		return $this->mFields;
	}

	/**
	 * This method returns the current row iterator
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @return array<FramsieTableMapper>
	 */
	public function getIterator() {
		// Return the current iterator
		return $this->mIterator;
	}

	/**
	 * This method returns the current table name
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @return string
	 */
	public function getTable() {
		// Return the current table
		return $this->mTable;
	}

	/**
	 * This method returns the current FramsieTableMapper class name
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @return string
	 */
	public function getTableMapper() {
		// Return the current table mapper class name
		return $this->mTableMapper;
	}

	/**
	 * This method returns the current unique ID column name
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @return string
	 */
	public function getUniqueIdentifierColumn() {
		// Return the current unique ID column name
		return $this->mUniqueIdentifierColumn;
	}

	/**
	 * This method returns the current set of WHERE clauses
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @return array
	 */
	public function getWhereClauses() {
		// Return the current WHERE clauses
		return $this->mWhereClauses;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets an external instance of FramsieDatabaseInterface into the class
	 * @package Framsie
	 * @subpackage FramsieTableMapper
	 * @access public
	 * @param FramsieDatabaseInterface $oInstance
	 * @return FramsieTableLoader $this
	 */
	public function setDBI(FramsieDatabaseInterface $oInstance) {
		// Set the DBI into the class
		$this->mDBI = $oInstance;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the list of fields to return into the class
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @param array $aFields
	 * @return FramsieTableLoader $this
	 */
	public function setFields(array $aFields) {
		// Set the fields into the class
		$this->mFields = $aFields;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the table name into the instance
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @param string $sTable
	 * @return FramsieTableLoader $this
	 */
	public function setTable($sTable) {
		// Set the table name into the instance
		$this->mTable = (string) $sTable;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the FramsieTableMapper class name into the instance
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @param string $sClassName
	 * @return FramsieTableLoader $this
	 */
	public function setTableMapper($sClassName) {
		// Set the FramsieTableMapper class name into the class
		$this->mTableMapper = (string) $sClassName;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the unique ID column name into the class
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @param string $sColumnName
	 * @return FramsieTableLoader $this
	 */
	public function setUniqueIdentifierColumn($sColumnName) {
		// Set the unique ID column name into the class
		$this->mUniqueIdentifierColumn = (string) $sColumnName;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the WHERE clauses array into the class
	 * @package Framsie
	 * @subpackage FramsieTableLoader
	 * @access public
	 * @param array $aWhere
	 * @return FramsieTableLoader $this
	 */
	public function setWhereClauses(array $aWhere) {
		// Set the WHERE clauses into the class
		$this->mWhereClauses = $aWhere;
		// Return the instance
		return $this;
	}
}
