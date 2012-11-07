<?php

class FramsieDatabaseMapper {

	////////////////////////////////////////////////////////////////////////////
	/// Properties ////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the name of the database that we will be mapping
	 * @access protected
	 * @var string
	 */
	protected $mDatabase = null;

	/**
	 * This proeprty contains the FramsieTableMapper instances for each table
	 * @access protected
	 * @var array
	 */
	protected $mTables   = array();

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constructor simply initializes the class
	 * @package Framsie
	 * @subpackage FramsieDatabaseMapper
	 * @access public
	 * @param string $sDatabase
	 * @return FramsieDatabaseMapper $this
	 */
	public function __construct($sDatabase = null) {
		// Check to see if a database name was loaded
		if (empty($sDatabase) === false) {
			// Set the database
			$this->setDatabase($sDatabase);
		}
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method loads the database tables into the system
	 * @package Framsie
	 * @subpackage FramsieDatabaseMapper
	 * @access public
	 * @return FramsieDatabaseMapper $this
	 */
	public function load() {
		// Initialize the DBI
		$aTables = FramsieDatabaseInterface::getInstance(true) // We want a new instance
			->setDatabase($this->mDatabase)         // Set the database
			->getTables();                          // Grab the tables
		// Loop through the tables
		foreach ($aTables as $aTableName) {
			// Grab the primary key
			$oPrimaryKey = FramsieDatabaseInterface::getInstance(true)->setTable($aTableName[0])->getTablePrimaryKey();
			// Check to see if this table has a primary key
			if ($oPrimaryKey === false) {
				// Append the table and instantiate the mapper
				$this->mTables[$aTableName[0]] = new FramsieTableMapper($sTableName, null);
				// This table is a lookup table
				$this->mTables[$aTableName[0]]->setIsLookupTable(true);
				// We're done with this iteration
				continue;
			}
			// Append the table and instantiate the mapper
			$this->mTables[$aTableName[0]] = new FramsieTableMapper($sTableName, $oPrimaryKey->Column_name);
		}
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the name of the current database
	 * @package Framsie
	 * @subpackage FramsieDatabaseMapper
	 * @access public
	 * @return string
	 */
	public function getDatabase() {
		// Return the database
		return $this->mDatabase;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the database name into the system
	 * @package Framsie
	 * @subpackage FramsieDatabaseMapper
	 * @access public
	 * @param string $sDatabase
	 * @return FramsieDatabaseMapper $this
	 */
	public function setDatabase($sDatabase) {
		// Set the database into the system
		$this->mDatabase = (string) $sDatabase;
		// Return the instance
		return $this;
	}
}