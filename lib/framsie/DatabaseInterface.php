<?php
/**
 * This class provides an interface to a Database
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieDatabaseInterface {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant defines the AND conditional keyword
	 * @var string
	 */
	const ANDCON          = 'AND';

	/**
	 * This constant defines tha ASC order by keyword
	 * @var string
	 */
	const ASCORD          = 'ASC';

	/**
	 * This constant defines the skeleton of a DELETE query
	 * @var string
	 */
	const DELETEQUERY     = 'DELETE FROM :sTable :sWhere;';

	/**
	 * This constant defines the DESC order by keyword
	 * @var string
	 */
	const DESCORD         = 'DESC';

	/**
	 * This constant defines the = (equal to) operator
	 * @var string
	 */
	const EQOP            = '=';

	/**
	 * This constant defines the skeleton of a FULL JOIN statement
	 * @var string
	 */
	const FULLJOINQUERY   = 'FULL JOIN :sJoinTable :sAsAlias ON (:sJoinAlias.:sJoinField = :sFromAlias.:sFromField)';

	/**
	 * This constant defines the >= (greater than or equal to) operator
	 * @var string
	 */
	const GTEQOP          = '>=';

	/**
	 * This constant defines the > (greater than) operator
	 * @var string
	 */
	const GTOP            = '>';

	/**
	 * This constant defines the skeleton of an INNER JOIN statement
	 * @var string
	 */
	const INNERJOINQUERY  = 'INNER JOIN :sJoinTable :sAsAlias ON (:sJoinAlias.:sJoinField = :sFromAlias.:sFromField)';

	/**
	 * This constant defines the skeleton of an INSERT statement
	 * @var string
	 */
	const INSERTQUERY     = 'INSERT INTO :sTable (:aFields) VALUES (:aValues);';

	/**
	 * This constant contains the definitions for Microsoft SQL interactions
	 * @var integer
	 */
	const INTERFACE_MSSQL = 1;

	/**
	 * This constant contains the definition for MySQL interactions
	 * @var integer
	 */
	const INTERFACE_MYSQL = 2;

	/**
	 * This constant contains the definitions for Oracle interactions
	 * @var integer
	 */
	const INTERFACE_OCI   = 3;

	/**
	 * This constant contains the definition for PostgreSQL interactions
	 * @var integer
	 */
	const INTERFACE_PGSQL = 4;

	/**
	 * This constant defines the skeleton of a LEFT JOIN statement
	 * @var string
	 */
	const LEFTJOINQUERY   = 'LEFT JOIN :sJoinTable :sAsAlias ON (:sJoinAlias.:sJoinField = :sFromAlias.:sFromField)';

	/**
	 * This constant defines the LIKE operator
	 * @var string
	 */
	const LIKEOP          = 'LIKE';

	/**
	 * This constant defines the <= (less than or equal to) operator
	 * @var string
	 */
	const LTEQOP          = '<=';

	/**
	 * This constant defines the < (less than) operator
	 * @var string
	 */
	const LTOP            = '<';

	/**
	 * This constant contains the Microsoft SQL column and table name wrapper
	 * @var string
	 */
	const MSSQL_WRAPPER   = '[:sTableColumn]';

	/**
	 * This constant contains the MySQL column and table name wrapper
	 * @var string
	 */
	const MYSQL_WRAPPER   = '`:sTableColumn`';

	/**
	 * This constant defines the <> (!= or not equal to) operator
	 * @var string
	 */
	const NEQOP           = '<>';

	/**
	 * This constant contains the Oracle column and table name wrapper
	 * @var string
	 */
	const OCI_WRAPPER     = ':sTableColumn';

	/**
	 * This constant defines the OR conditional keyword
	 * @var string
	 */
	const ORCON           = 'OR';

	/**
	 * This constant contains the PostgreSQL column and table name wrapper
	 * @var string
	 */
	const PGSQL_WRAPPER   = '":sTableColumn"';

	/**
	 * This constant defines the skeleton of a RIGHT JOIN statement
	 * @var string
	 */
	const RIGHTJOINQUERY  = 'RIGHT JOIN :sJoinTable :sAsAlias ON (:sJoinAlias.:sJoinField = :sFromAlias.:sFromField)';

	/**
	 * This constant defines the skeleton of a SELECT statement
	 * @var string
	 */
	const SELECTQUERY     = 'SELECT :aFields FROM :sTable :sAsAlias :aJoins :sWhere :sGroupBy :sOrderBy :sLimit;';

	/**
	 * This constant defines the skeleton of an UPDATE statement
	 * @var string
	 */
	const UPDATEQUERY     = 'UPDATE :sTable SET :aFieldValuePairs :sWhere;';

	/**
	 * This constant defines the SQL wildcard
	 * @var string
	 */
	const WILDCARD        = '*';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This is our instance placeholder
	 * @staticvar
	 * @access protected
	 * @var FramsieDatabaseInterface
	 */
	protected static $mInstance  = null;

	/**
	 * This is our database connection placeholder
	 * @access protected
	 * @var PDO
	 */
	protected $mConnection       = null;

	/**
	 * This is the current query being generated or executed
	 * @access protected
	 * @var string
	 */
	protected $mCurrentQuery     = null;

	/**
	 * This is the current statement being processed
	 * @access protected
	 * @var PDOStatement
	 */
	protected $mCurrentStatement = null;

	/**
	 * This method stores the last fetched resultset
	 * @access protected
	 * @var PDORow
	 */
	protected $mCurrentResults   = null;

	/**
	 * This property contains the name of the database
	 * @access protected
	 * @var string
	 */
	protected $mDatabase         = null;

	/**
	 * This property contains the field/value pairs associated
	 * with the current query
	 * @access protected
	 * @var array
	 */
	protected $mFields           = array();

	/**
	 * This property contains the definition for the interface we will be using
	 * @access protected
	 * @var integer
	 */
	protected $mInterface        = self::INTERFACE_MYSQL;

	/**
	 * This property contains the generated JOIN statements
	 * associated with the current query
	 * @access protected
	 * @var string
	*/
	protected $mJoins            = array(
			'aFull'  => array(),
			'aInner' => array(),
			'aLeft'  => array(),
			'aRight' => array()
	);

	/**
	 * This property contains the group by arguments associated
	 * with the current query
	 * @access protected
	 * @var array
	*/
	protected $mGroupBy          = array();

	/**
	 * This property contains the limit argumetns associated
	 * with the current query
	 * @access protected
	 * @var array
	*/
	protected $mLimits           = array();

	/**
	 * This property contains the order by arguments
	 * associated with the current query
	 * @access protected
	 * @var array
	*/
	protected $mOrderBy          = array(
			'aAscending'  => array(),
			'aDescending' => array()
	);

	/**
	 * This property stores the current query in all its glory
	 * @access protected
	 * @var array
	*/
	protected $mQuery          = null;

	/**
	 * This property keeps track of the status of the current
	 * query and what has been generated
	 * @access protected
	 * @var array
	 */
	protected $mQueryStatus      = array(
			'bFields'  => false,
			'bGroupBy' => false,
			'bJoins'   => false,
			'bLimit'   => false,
			'bOrderBy' => false,
			'bWhere'   => false
	);

	/**
	 * This property stores the name of the table we will be working with
	 * @access protected
	 * @var string
	*/
	protected $mTable            = null;

	/**
	 * This property stores the alias name for the table we will be working with
	 * @access protected
	 * @var string
	 */
	protected $mTableAlias       = null;


	protected $mWhereClauses     = array();

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method creates and ensures there is always a single instance
	 * of this class floating around
	 * @package FramsieDatabaseInterface
	 * @static
	 * @access public
	 * @param boolean [$bReset]
	 * @return FramsieDatabaseInterface self::$mInstance
	*/
	public static function getInstance($bReset = false) {
		// Check for an existing instance or a reset notification
		if (empty(self::$mInstance) || ($bReset === true)) {
			// Create a new instance of the class
			self::$mInstance = new self();
		}
		// Return the instance
		return self::$mInstance;
	}

	/**
	 * This method sets a custom instance of this class into itself,
	 * this is generally only used for testing with phpUnit
	 * @package FramsieDatabaseInterface
	 * @static
	 * @access public
	 * @param FramsieDatabaseInterface $oInstance
	 * @return FramsieDatabaseInterface self::$mInstance
	 */
	public static function setInstance(FramsieDatabaseInterface $oInstance) {
		// Set the new instance
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply sets the database connection into the system,
	 * it is protected so that we may enforce the singleton pattern
	 * @package FramsieDatabaseInterface
	 * @access protected
	 * @return FramsieDatabaseInterface $this
	 */
	protected function __construct() {
		// Try to connect
		try {
			// Create the database connection
			$this->mConnection = new PDO(
					FramsieConfiguration::Load('database.dsn'),  // Data Source Name
					FramsieConfiguration::Load('database.user'), // Username
					FramsieConfiguration::Load('database.pass')   // Password
			);
			// Set the database name
			$this->mDatabase = (string) FramsieConfiguration::Load('database.dbname');
		} catch (PDOException $oException) {
			// Do something with the exception
		}
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method builds a DELETE query
	 * @package FramsieDatabaseInterface
	 * @subpackage Builders
	 * @access protected
	 * @uses @method buildWhereClause
	 * @return FramsieDatabaseInterface
	 */
	protected function buildDeleteQuery() {
		// Set the WHERE clause in the query
		$this->mQuery = (string) str_replace(':sWhere', $this->buildWhereClause(), $this->mQuery);
		// Return the instance
		return $this;
	}

	/**
	 * This method is responsible for building the GROUP BY clause
	 * @package FramsieDatabaseInterface
	 * @subpackage Builders
	 * @access protected
	 * @return string
	 */
	protected function buildGroupByClause() {
		// Create a GROUP BY placeholder
		$sGroupBys = (string) null;
		// Create an iterator
		$iIterator = 0;
		// Check for GROUP BY clause
		if (empty($this->mGroupBy) === false) {
			// Start the GROUP BY clause
			$sGroupBys = (string) 'GROUP BY ';
			// Loop through the clauses
			foreach ($this->mGroupBy as $sName) {
				// Is this the last field
				if (($iIterator + 1) === count($this->mGroupBy)) {
					// Add the field
					$sGroupBys .= (string) "{$this->quoteTableColumnName($sName)}";
					// Reset the iterator
					$iIterator = 0;
				} else {
					// Add the field
					$sGroupBys .= (string) "{$this->quoteTableColumnName($sName)},";
					// Increment the iterator
					$iIterator++;
				}
			}
		}
		// Return the GROUP BY clause
		return $sGroupBys;
	}

	/**
	 * This method generates an INSERT query
	 * @package FramsieDatabaseInterface
	 * @subpackage Builders
	 * @access protected
	 * @uses @method quoteTrueFieldValue
	 * @throws Exception
	 * @return FramsieDatabaseInterface $this
	 */
	protected function buildInsertQuery() {
		// Create a field placeholder
		$sFields   = (string) null;
		// Create a values placeholder
		$sValues   = (string) null;
		// Create an iterator
		$iIterator = 0;
		// Check for fields
		if (empty($this->mFields)) {
			// Throw an exception for we need fields
			throw new Exception('No fieldsets present, fieldsets are needed to generate valid INSERT statements.');
		}
		// Loop through the fields
		foreach ($this->mFields as $sName => $aField) {
			// Check to see if we are on the last field
			if (($iIterator + 1) === count($this->mFields)) {
				// Set the field into the query
				$sFields .= (string) "{$this->quoteTableColumnName($sName)}";
				// Set the value into the query
				$sValues .= (string) $this->quoteTrueFieldValue($aField['sValue']);
				// Reset the iterator
				$iIterator = 0;
			} else {
				// Set the field into the query
				$sFields .= (string) "{$this->quoteTableColumnName($sName)}, ";
				// Set the value into the query
				$sValues .= (string) "{$this->quoteTrueFieldValue($aField['sValue'])}, ";
				// Increment the iterator
				$iIterator++;
			}
		}
		// Set the fields and values into the main query
		$this->mQuery = (string) str_replace(array(
				':aFields', ':aValues' // Keys
		), array(
				$sFields, $sValues     // Values
		), $this->mQuery);
		// We're done, return the instance
		return $this;
	}

	/**
	 * This method pieces together all of the JOIN statements from the system
	 * @package FramsieDatabaseInterface
	 * @subpackage Builders
	 * @access protected
	 * @return string
	 */
	protected function buildJoins() {
		// Create a JOIN statement placeholder
		$sJoins = (string) null;
		// Check for FULL JOIN statements
		if (empty($this->mJoins['aFull']) === false) {
			// Loop through the FULL JOIN statements
			foreach ($this->mJoins['aFull'] as $sFullJoinStatement) {
				// Append the JOIN
				$sJoins .= (string) $sFullJoinStatement;
			}
		}
		// Check for INNER JOIN statements
		if (empty($this->mJoins['aInner']) === false) {
			// Loop through the INNER JOIN statements
			foreach ($this->mJoins['aInner'] as $sInnerJoinStatement) {
				// Append the JOIN
				$sJoins .= (string) $sInnerJoinStatement;
			}
		}
		// Check for LEFT JOIN statements
		if (empty($this->mJoins['aLeft']) === false) {
			// Loop through the LEFT JOIN statements
			foreach ($this->mJoins['aLeft'] as $sLeftJoinStatement) {
				// Append the JOIN
				$sJoins .= (string) $sLeftJoinStatement;
			}
		}
		// Check for RIGHT JOIN statements
		if (empty($this->mJoins['aRight']) === false) {
			// Loop through the RIGHT JOIN statements
			foreach ($this->mJoins['aRight'] as $sRightJoinStatement) {
				// Append the JOIN
				$sJoins .= (string) $sRightJoinStatement;
			}
		}
		// Return the JOIN statements
		return $sJoins;
	}

	/**
	 * This method is responsible for building the LIMIT clause
	 * @package FramsieDatabaseInterface
	 * @subpackage Builders
	 * @access protected
	 * @return string
	 */
	protected function buildLimitClause() {
		// Create a LIMIT placeholder
		$sLimits = (string) null;
		// Check to see if we have a LIMIT clause
		if (empty($this->mLimits) === false) {
			// Start the LIMIT clause
			$sLimits = (string) 'LIMIT ';
			// Check to see if we have a range
			if (count($this->mLimits) === 2) { // We have a range
				// Set the LIMIT range
				$sLimits .= (string) "{$this->mLimits[0]},{$this->mLimits[1]}";
			} else {                           // We just want N number of rows
				// Set the LIMIT
				$sLimits .= (string) $this->mLimits;
			}
		}
		// Return the LIMIT clause
		return $sLimits;
	}

	/**
	 * This method is responsible for generating the ORDER BY clause
	 * @package FramsieDatabaseInterface
	 * @subpackage Builders
	 * @access protected
	 * @return string
	 */
	protected function buildOrderByClause() {
		// Create an ORDER BY placeholder
		$sOrderBys = (string) null;
		// Create an iterator
		$iIterator = 0;
		// Check for ORDER BY clasuse
		if ((empty($this->mOrderBy['aAscending']) === false) || (empty($this->mOrderBy['aDescending']) === false)) {
			// Start the ORDER BY clause
			$sOrderBys = (string) "ORDER BY ";
			// Do we have ascending fields
			if (empty($this->mOrderBy['aAscending']) === false) {
				// Loop through ascending clause
				foreach ($this->mOrderBy['aAscending'] as $sName) {
					// Append the field
					$sOrderBys .= (string) "{$this->quoteTableColumnName($sName)} ";
				}
				// Add the direction
				$sOrderBys .= (string) self::ASCORD;
			}
			// Check for descending fields
			if (empty($this->mOrderBy['aDescending']) === false) {
				// Append to the clause
				$sOrderBys .= (string) ((empty($this->mOrderBy['aAscending']) === false) ? ', ' : null);
				// Loop through the descending fields
				foreach ($this->mOrderBy['aDescending'] as $sName) {
					// Append the field
					$sOrderBys .= (string) "{$this->quoteTableColumnName($sName)} ";
				}
				// Add the direction
				$sOrderBys .= (string) self::DESCORD;
			}
		}
		// Return the ORDER BY clause
		return $sOrderBys;
	}

	/**
	 * This method is responsible for building SELECT statements
	 * @package FramsieDatabaseInterface
	 * @subpackage Builders
	 * @access protected
	 * @uses @method buildWhereClause
	 * @uses @method buildJoins
	 * @uses @method buildGroupByClause
	 * @uses @method buildOrderByClause
	 * @uses @method buildLimitClause
	 * @throws Exception
	 * @return FramsieDatabaseInterface
	 */
	protected function buildSelectQuery() {
		// Create a fields placeholder
		$sFields   = (string) null;
		// Create an iterator
		$iIterator = 0;
		// Check for fieldsets
		if (empty($this->mFields)) {
			// Throw an exception because we need fieldsets
			throw new Exception('No fieldsets present, fieldsets are needed to generate valid SELECT statements.');
		}
		// Loop through the fieldsets
		foreach ($this->mFields as $sColumn => $aData) {
			// Check for a table name
			if (empty($aData['sTable']) === false) {
				// Add the table
				$sFields .= (string) "{$this->quoteTableColumnName($aData['sTable'])}.";
			}
			// Add the field
			$sFields .= (string) (($sColumn === '*') ? "*" : "{$this->quoteTableColumnName($sColumn)}");
			// Check the iterator
			if (($iIterator + 1) === count($this->mFields)) {
				// Reset the iterator
				$iIterator = 0;
			} else {
				// Append a separator
				$sFields .= (string) ', ';
				// Increment the iterator
				$iIterator++;
			}
		}
		// Set the fields into the query
		$this->mQuery = (string) str_replace(':aFields',  $sFields,                    $this->mQuery);
		// Set the WHERE clause into the query
		$this->mQuery = (string) str_replace(':sWhere',   $this->buildWhereClause(),   $this->mQuery);
		// Set the JOIN statements into the query
		$this->mQuery = (string) str_replace(':aJoins',   $this->buildJoins(),         $this->mQuery);
		// Set the GROUP BY clause
		$this->mQuery = (string) str_replace(':sGroupBy', $this->buildGroupByClause(), $this->mQuery);
		// Set the ORDER BY clause
		$this->mQuery = (string) str_replace(':sOrderBy', $this->buildOrderByClause(), $this->mQuery);
		// Set the LIMIT clause
		$this->mQuery = (string) str_replace(':sLimit',   $this->buildLimitClause(),   $this->mQuery);
		// We're done, return the instance
		return $this;
	}

	/**
	 * This method generates an UPDATE statement
	 * @package FramsieDatabaseInterface
	 * @subpackage Builders
	 * @access protected
	 * @uses @method quoteTrueFieldValue
	 * @uses @method buildWhereClause
	 * @throws Exception
	 * @return FramsieDatabaseInterface $this
	 */
	protected function buildUpdateQuery() {
		// Create a fields placeholder
		$sFields   = (string) null;
		// Create an iterator
		$iIterator = 0;
		// Check for fieldsets
		if (empty($this->mFields)) {
			// We need fieldsets to continue, throw an exception
			throw new Exception('No fieldsets present, fieldsets are needed to generate valid UPDATE statements.');
		}
		// Loop through the fields
		foreach ($this->mFields as $sName => $aField) {
			// Is this the last field
			if (($iIterator + 1) === count($this->mFields)) {
				// Append the field
				$sFields .= (string) "{$this->quoteTableColumnName($sName)} = {$this->quoteTrueFieldValue($aField['sValue'])}";
				// Reset the iterator
				$iIterator = 0;
			} else {
				// Append the field
				$sFields .= (string) "{$this->quoteTableColumnName($sName)} = {$this->quoteTrueFieldValue($aField['sValue'])}, ";
				// Increment the iterator
				$iIterator++;
			}
		}
		// Sets the fields into the query
		$this->mQuery = (string) str_replace(':aFieldValuePairs', $sFields,                  $this->mQuery);
		// Set the WHERE clause into the query
		$this->mQuery = (string) str_replace(':sWhere',           $this->buildWhereClause(), $this->mQuery);
		// We're done, return the instance
		return $this;
	}

	/**
	 * This method is responsible for the generation of the WHERE clause
	 * @package FramsieDatabaseInterface
	 * @subpackage Builders
	 * @access protected
	 * @return string
	 */
	protected function buildWhereClause() {
		// Nullify the WHERE clause by default
		$sWhere    = (string) null;
		// Create an iterator
		$iIterator = 0;
		// Check for defined WHERE clauses
		if (empty($this->mWhereClauses) === false) {
			// Start the WHERE clause
			$sWhere = (string) "WHERE ";
			// Loop through the clauses
			foreach ($this->mWhereClauses as $aClause) {
				// Check to see if this is the first clause
				if ($iIterator === 0) {
					// Check for a table
					if (empty($aClause['sTable'])) {
						$sWhere .= (string) "{$this->quoteTableColumnName($aClause['sField'])} {$aClause['sOperator']} {$this->quoteTrueFieldValue($aClause['sValue'])} ";
					} else {
						// Set the clause
						$sWhere .= (string) "{$this->quoteTableColumnName($aClause['sTable'])}.{$this->quoteTableColumnName($aClause['sField'])} {$aClause['sOperator']} {$this->quoteTrueFieldValue($aClause['sValue'])} ";
					}
					// Increment the iterator
					$iIterator++;
				} else {
					// Check for a table
					if (empty($aClause['sTable'])) {
						// Set the clause
						$sWhere .= (string) "{$aClause['sConditional']} {$this->quoteTableColumnName($aClause['sField'])} {$aClause['sOperator']} {$this->quoteTrueFieldValue($aClause['sValue'])} ";
					} else {
						// Set the clause
						$sWhere .= (string) "{$aClause['sConditional']} {$this->quoteTableColumnName($aClause['sTable'])}.{$this->quoteTableColumnName($aClause['sField'])} {$aClause['sOperator']} {$this->quoteTrueFieldValue($aClause['sValue'])} ";
					}
					// Increment the iterator
					$iIterator++;
				}
			}
		}
		// Return the WHERE clause
		return $sWhere;
	}

	/**
	 * This method properly wraps table and column names based on the chosen interface
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access protected
	 * @param string $sEntity
	 * @return string
	 */
	protected function quoteTableColumnName($sEntity) {
		// Determine the interface type
		switch ($this->mInterface) {
			// Oracle
			case self::INTERFACE_OCI   : return str_replace(':sTableColumn', $sEntity, self::OCI_WRAPPER);   break;
			// Microsoft SQL
			case self::INTERFACE_MSSQL : return str_replace(':sTableColumn', $sEntity, self::MSSQL_WRAPPER); break;
			// MySQL
			case self::INTERFACE_MYSQL : return str_replace(':sTableColumn', $sEntity, self::MYSQL_WRAPPER); break;
			// PostgreSQL
			case self::INTERFACE_PGSQL : return str_replace(':sTableColumn', $sEntity, self::PGSQL_WRAPPER); break;
		}
	}

	/**
	 * This method checks the value for SQL functions and quotes accordingly
	 * @package FramsieDatabaseInterface
	 * @access protected
	 * @param string $sValue
	 * @return string
	 */
	protected function quoteTrueFieldValue($sValue) {
		// Check for SQL functions
		if (preg_match('/^([a-zA-Z]+\([a-zA-Z0-9_-`"\.]+\))$/', $sValue)) { // A SQL function exists
			// Simply return the value as is
			return $sValue;
		}
		// Return the quoted value
		return $this->mConnection->quote($sValue);
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds a single field to the query
	 * @package FramsieDatabaseInterface
	 * @access public
	 * @param string $sName
	 * @param multitype [$sValue]
	 * @param string [$sTable]
	 * @return FramsieDatabaseInterface $this
	 */
	public function addField($sName, $sValue = null, $sTable = null) {
		// Add the field to the list
		$this->mFields[(string) $sName] = array(
			'sTable' => (string) $sTable,
			'sValue' => $sValue
		);
		// Return the instance
		return $this;
	}

	/**
	 * This method generates the FULL JOIN portion of the query and
	 * sets it in to the system
	 * @package FramsieDatabaseInterface
	 * @access public
	 * @param string $sJoinTable
	 * @param string $sJoinTableField
	 * @param string $sFromTableField
	 * @param string [$sJoinTableAlias]
	 * @return FramsieDatabaseInterface $this
	 */
	public function addFullJoin($sJoinTable, $sJoinTableField, $sFromTableField, $sJoinTableAlias = null) {
		// Grab the query skeleton
		$sJoinQuery = (string) self::FULLJOINQUERY;
		// Set the JOIN table, the JOIN field and the from field
		$sJoinQuery = (string) str_replace(array(
				':sJoinTable', ':sJoinField', ':sFromField'                                                                                            // Keys
		), array(
				$this->quoteTableColumnName($sJoinTable), $this->quoteTableColumnName($sJoinTableField), $this->quoteTableColumnName($sFromTableField) // Values
		), $sJoinQuery);
		// Check for a JOIN table alias
		if (empty($sJoinTableAlias) === false) {
			// Set the alias
			$sJoinQuery = (string) str_replace(array(
					':sAsAlias', ':sJoinAlias'                                                                          // Keys
			), array(
					"AS {$this->quoteTableColumnName($sJoinTableAlias)}", $this->quoteTableColumnName($sJoinTableAlias) // Values
			), $sJoinQuery);
		} else { // No alias was provided, so we use the table name
			// Set the table name
			$sJoinQuery = (string) str_replace(array(
					':sAsAlias', ':sJoinAlias'                     // Keys
			), array(
					null, $this->quoteTableColumnName($sJoinTable) // Values
			), $sJoinQuery);
		}
		// Check for a from table alias
		if (empty($this->mTableAlias)) { // No alias was provided
			// Set the alias as the table name
			$sJoinQuery = (string) str_replace(':sFromAlias', $this->quoteTableColumnName($this->mTable), $sJoinQuery);
		} else {                         // An alias exists
			// Set the alias
			$sJoinQuery = (string) str_replace(':sFromAlias', $this->quoteTableColumnName($this->mTableAlias), $sJoinQuery);
		}
		// Set the JOIN statement in to the system
		array_push($this->mJoins['aFull'], $sJoinQuery);
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a field to the GROUP BY array
	 * @package DirectoryService
	 * @access public
	 * @param string $sName
	 * @return FramsieDatabaseInterface $this
	 */
	public function addGroupByField($sName) {
		// Add the field
		array_push($this->mGroupBy, $sName);
		// Return the instance
		return $this;
	}

	/**
	 * This method generates the INNER JOIN portion of the query and
	 * sets it in to the system
	 * @package FramsieDatabaseInterface
	 * @access public
	 * @param string $sJoinTable
	 * @param string $sJoinTableField
	 * @param string $sFromTableField
	 * @param string [$sJoinTableAlias]
	 * @return FramsieDatabaseInterface $this
	 */
	public function addInnerJoin($sJoinTable, $sJoinTableField, $sFromTableField, $sJoinTableAlias = null) {
		// Grab the query skeleton
		$sJoinQuery = (string) self::INNERJOINQUERY;
		// Set the JOIN table, the JOIN field and the from field
		$sJoinQuery = (string) str_replace(array(
				':sJoinTable', ':sJoinField', ':sFromField'                                                                                            // Keys
		), array(
				$this->quoteTableColumnName($sJoinTable), $this->quoteTableColumnName($sJoinTableField), $this->quoteTableColumnName($sFromTableField) // Values
		), $sJoinQuery);
		// Check for a JOIN table alias
		if (empty($sJoinTableAlias) === false) {
			// Set the alias
			$sJoinQuery = (string) str_replace(array(
					':sAsAlias', ':sJoinAlias'                                                                          // Keys
			), array(
					"AS {$this->quoteTableColumnName($sJoinTableAlias)}", $this->quoteTableColumnName($sJoinTableAlias) // Values
			), $sJoinQuery);
		} else { // No alias was provided, so we use the table name
			// Set the table name
			$sJoinQuery = (string) str_replace(array(
					':sAsAlias', ':sJoinAlias'                     // Keys
			), array(
					null, $this->quoteTableColumnName($sJoinTable) // Values
			), $sJoinQuery);
		}
		// Check for a from table alias
		if (empty($this->mTableAlias)) { // No alias was provided
			// Set the alias as the table name
			$sJoinQuery = (string) str_replace(':sFromAlias', $this->quoteTableColumnName($this->mTable), $sJoinQuery);
		} else {                         // An alias exists
			// Set the alias
			$sJoinQuery = (string) str_replace(':sFromAlias', $this->quoteTableColumnName($this->mTableAlias), $sJoinQuery);
		}
		// Set the JOIN statement in to the system
		array_push($this->mJoins['aInner'], $sJoinQuery);
		// Return the instance
		return $this;
	}

	/**
	 * This method generates the LEFT JOIN portion of the query and
	 * sets it in to the system
	 * @package FramsieDatabaseInterface
	 * @access public
	 * @param string $sJoinTable
	 * @param string $sJoinTableField
	 * @param string $sFromTableField
	 * @param string [$sJoinTableAlias]
	 * @return FramsieDatabaseInterface $this
	 */
	public function addLeftJoin($sJoinTable, $sJoinTableField, $sFromTableField, $sJoinTableAlias = null) {
		// Grab the query skeleton
		$sJoinQuery = (string) self::LEFTJOINQUERY;
		// Set the JOIN table, the JOIN field and the from field
		$sJoinQuery = (string) str_replace(array(
				':sJoinTable', ':sJoinField', ':sFromField'                                                                                            // Keys
		), array(
				$this->quoteTableColumnName($sJoinTable), $this->quoteTableColumnName($sJoinTableField), $this->quoteTableColumnName($sFromTableField) // Values
		), $sJoinQuery);
		// Check for a JOIN table alias
		if (empty($sJoinTableAlias) === false) {
			// Set the alias
			$sJoinQuery = (string) str_replace(array(
					':sAsAlias', ':sJoinAlias'                                                                          // Keys
			), array(
					"AS {$this->quoteTableColumnName($sJoinTableAlias)}", $this->quoteTableColumnName($sJoinTableAlias) // Values
			), $sJoinQuery);
		} else { // No alias was provided, so we use the table name
			// Set the table name
			$sJoinQuery = (string) str_replace(array(
					':sAsAlias', ':sJoinAlias'                     // Keys
			), array(
					null, $this->quoteTableColumnName($sJoinTable) // Values
			), $sJoinQuery);
		}
		// Check for a from table alias
		if (empty($this->mTableAlias)) { // No alias was provided
			// Set the alias as the table name
			$sJoinQuery = (string) str_replace(':sFromAlias', $this->quoteTableColumnName($this->mTable), $sJoinQuery);
		} else {                         // An alias exists
			// Set the alias
			$sJoinQuery = (string) str_replace(':sFromAlias', $this->quoteTableColumnName($this->mTableAlias), $sJoinQuery);
		}
		// Set the JOIN statement in to the system
		array_push($this->mJoins['aLeft'], $sJoinQuery);
		// Return the instance
		return $this;
	}

	/**
	 * This method adds an ORDER BY clause to the system
	 * @package DirectoryService
	 * @access public
	 * @param string $sField
	 * @param string $sDirection
	 * @return FramsieDatabaseInterface $this
	 */
	public function addOrderBy($sField, $sDirection = self::ASCORD) {
		// Add the ORDER BY parameter
		array_push($this->mOrderBy[(($sDirection === self::ASCORD) ? 'aAscending' : 'aDescending')], $sField);
		// Return the instance
		return $this;
	}

	/**
	 * This method generates the RIGHT JOIN portion of the query and
	 * sets it in to the system
	 * @package FramsieDatabaseInterface
	 * @access public
	 * @param string $sJoinTable
	 * @param string $sJoinTableField
	 * @param string $sFromTableField
	 * @param string [$sJoinTableAlias]
	 * @return FramsieDatabaseInterface $this
	 */
	public function addRightJoin($sJoinTable, $sJoinTableField, $sFromTableField, $sJoinTableAlias = null) {
		// Grab the query skeleton
		$sJoinQuery = (string) self::RIGHTJOINQUERY;
		// Set the JOIN table, the JOIN field and the from field
		$sJoinQuery = (string) str_replace(array(
				':sJoinTable', ':sJoinField', ':sFromField'                                                                                            // Keys
		), array(
				$this->quoteTableColumnName($sJoinTable), $this->quoteTableColumnName($sJoinTableField), $this->quoteTableColumnName($sFromTableField) // Values
		), $sJoinQuery);
		// Check for a JOIN table alias
		if (empty($sJoinTableAlias) === false) {
			// Set the alias
			$sJoinQuery = (string) str_replace(array(
					':sAsAlias', ':sJoinAlias'                                                                          // Keys
			), array(
					"AS {$this->quoteTableColumnName($sJoinTableAlias)}", $this->quoteTableColumnName($sJoinTableAlias) // Values
			), $sJoinQuery);
		} else { // No alias was provided, so we use the table name
			// Set the table name
			$sJoinQuery = (string) str_replace(array(
					':sAsAlias', ':sJoinAlias'                     // Keys
			), array(
					null, $this->quoteTableColumnName($sJoinTable) // Values
			), $sJoinQuery);
		}
		// Check for a from table alias
		if (empty($this->mTableAlias)) { // No alias was provided
			// Set the alias as the table name
			$sJoinQuery = (string) str_replace(':sFromAlias', $this->quoteTableColumnName($this->mTable), $sJoinQuery);
		} else {                         // An alias exists
			// Set the alias
			$sJoinQuery = (string) str_replace(':sFromAlias', $this->quoteTableColumnName($this->mTableAlias), $sJoinQuery);
		}
		// Set the JOIN statement in to the system
		array_push($this->mJoins['aRight'], $sJoinQuery);
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a where clause to the system
	 * @package FramsieDatabaseInterface
	 * @access public
	 * @param string $sField
	 * @param string $sValue
	 * @param string [$sTable]
	 * @param string [$sOperator]
	 * @param string [$sConditional]
	 * @return FramsieDatabaseInterface $this
	 */
	public function addWhereClause($sField, $sValue, $sTable = null, $sOperator = self::EQOP, $sConditional = self::ANDCON) {
		// Add the clause to the system
		array_push($this->mWhereClauses, array(
		'sConditional' => (string) $sConditional,
		'sField'       => (string) $sField,
		'sOperator'    => (string) $sOperator,
		'sTable'       => (string) $sTable,
		'sValue'       => (string) $sValue
		));
		// Return the instance
		return $this;
	}

	/**
	 * This method handles the generation of the entire query string
	 * @package FramsieDatabaseInterface
	 * @access public
	 * @uses @method buildDeleteQuery
	 * @uses @method buildInsertQuery
	 * @uses @method buildSelectQuery
	 * @uses @method buildUpdateQuery
	 * @return FramsieDatabaseInterface $this
	 */
	public function generateQuery() {
		// Grab the query type
		$sQueryType   = (string) $this->mQuery;
		// Set the table name
		$this->mQuery = (string) str_replace(':sTable', $this->quoteTableColumnName($this->mTable), $this->mQuery);
		// Check for a table alias
		if (empty($this->mTableAlias)) {
			// Remove the table alias
			$this->mQuery = (string) str_replace(':sAsAlias', null, $this->mQuery);
		} else {
			// Set the table alias
			$this->mQuery = (string) str_replace(':sAsAlias', "AS {$this->quoteTableColumnName($this->mTableAlias)}", $this->mQuery);
		}
		// Determine the query type
		switch($sQueryType) {
			case self::DELETEQUERY : $this->buildDeleteQuery(); break; // DELETE
			case self::INSERTQUERY : $this->buildInsertQuery(); break; // INSERT
			case self::SELECTQUERY : $this->buildSelectQuery(); break; // SELECT
			case self::UPDATEQUERY : $this->buildUpdateQuery(); break; // UPDATE
		}
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method fetches a column's meta data
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @param string $sColumn
	 * @return object
	 */
	public function getColumnMeta($sColumn) {
		// Setup the query
		$sQuery      = (string) "SHOW COLUMNS FROM {$this->quoteTableColumnName($this->mTable)} WHERE {$this->quoteTableColumnName('Field')} = :sColumn;";
		// Prepare the statement
		$oColumnMeta = $this->mConnection->prepare($sQuery);
		// Bind the column name
		$oColumnMeta->bindValue(':sColumn', $sColumn);
		// Execute the statement
		$oColumnMeta->execute();
		// Return the fetched results
		return $oColumnMeta->fetch(PDO::FETCH_OBJ);
	}

	/**
	 * This method fetches the column names from the database table
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access public
	 * @param boolean $bIncludeMeta
	 * @return array
	 */
	public function getColumns($bIncludeMeta = true) {
		// Setup the query
		$sQuery            = (string) "DESCRIBE {$this->quoteTableColumnName($this->mTable)};";
		// Setup the query
		$oTableDescription = $this->mConnection->prepare($sQuery);
		// Execute the statement
		$oTableDescription->execute();
		// Determine if we need the meta data
		if ($bIncludeMeta === true) {
			// Create a column placeholder
			$aColumns = array();
			// Loop through the results
			foreach ($oTableDescription->fetchAll(PDO::FETCH_OBJ) as $oColumn) {
				// Add the column to the system
				$aColumns[$oColumn->Field] = $oColumn;
			}
			// Return the column map
			return $aColumns;
		}
		// Return the columns
		return $oTableDescription->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * This method returns the current database connection in the system
	 * @package FramsieDatabaseInterface
	 * @subpackage Getters
	 * @access public
	 * @return PDO $this->mConnection
	 */
	public function getConnection() {
		// Return the current database connection
		return $this->mConnection;
	}

	/**
	 * This method returns the database name
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access public
	 * @return string
	 */
	public function getDatabase() {
		// Return the database name
		return $this->mDatabase;
	}

	/**
	 * This method returns the current field set in the system
	 * @package FramsieDatabaseInterface
	 * @subpackage Getters
	 * @access public
	 * @return array $this->mFields
	 */
	public function getFields() {
		// Return the current field set
		return $this->mFields;
	}

	/**
	 * This method returns the interface that the current instance is using
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access public
	 * @return integer
	 */
	public function getInterface() {
		// Return the interface
		return $this->mInterface;
	}

	/**
	 * This method returns the last inserted ID from the connection
	 * @package FramsieDatabaseInterface
	 * @subpackage Getters
	 * @access public
	 * @return string
	 */
	public function getLastInsertId() {
		// Return the last inserted ID from the connection
		return $this->mConnection->lastInsertId();
	}

	/**
	 * This method returns the number of rows from the last executed statement
	 * @package FramsieDatabaseInterface
	 * @subpackage Getters
	 * @access public
	 * @return integer
	 */
	public function getNumRows() {
		// Return the number of rows
		return $this->getQueryStatement()->rowCount();
	}

	/**
	 * This method returns the current query
	 * @package FramsieDatabaseInterface
	 * @subpackage Getters
	 * @access public
	 * @return string $this->mQuery
	 */
	public function getQuery() {
		// Return the current query
		return $this->mQuery;
	}

	/**
	 * This method executes a statement and returns the results
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access public
	 * @return boolean
	 */
	public function getQueryExecutionStatus() {
		// Return an executed statement
		return $this->getQueryStatement()->execute();
	}

	/**
	 * This method returns a prepared statement of the current query
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access public
	 * @return PDOStatement
	 */
	public function getQueryStatement() {
		// Return the query statement
		return $this->mConnection->prepare($this->mQuery);
	}

	/**
	 * This method queries the database for a single row and returns it
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access protected
	 * @param integer $iFetchType
	 * @throws Exception
	 * @return mixed
	 */
	public function getRow($iFetchType = PDO::FETCH_OBJ) {
		// Grab the statement
		$oStatement = $this->getQueryStatement();
		// Execute the statement
		if (!$oStatement->execute()) {
			// Throw an exception because we could not execute the statement
			throw new Exception("The statement \"{$this->mQuery}\" could not be executed.");
		}
		// Return the fetched row
		return $oStatement->fetch($iFetchType);
	}

	/**
	 * This method queries the database for all the rows and returns them
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access public
	 * @param integer $iFetchType
	 * @throws Exception
	 * @return multitype
	 */
	public function getRows($iFetchType = PDO::FETCH_OBJ) {
		// Grab the statement
		$oStatement = $this->getQueryStatement();
		// Execute the statement
		if (!$oStatement->execute()) {
			// Throw an exception because we could not execute the statement
			throw new Exception("The statement \"{$this->mQuery}\" could not be executed.");
		}
		// Return the results
		return $oStatement->fetchAll($iFetchType);
	}

	/**
	 * This method returns the current table name
	 * @package FramsieDatabaseInterface
	 * @subpackage Getters
	 * @access public
	 * @return string $this->mTable
	 */
	public function getTable() {
		// Return the current table name
		return $this->mTable;
	}

	/**
	 * This methos returns the current table alias name
	 * @package FramsieDatabaseInterface
	 * @subpackage Getters
	 * @return string $this->mTableAlias
	 */
	public function getTableAlias() {
		// Return the current table alias name
		return $this->mTableAlias;
	}

	/**
	 * This method returns the current database table's meta information
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access public
	 * @return object
	 */
	public function getTableMeta() {
		// Setup the query
		$sQuery     = (string) "SELECT * FROM {$this->quoteTableColumnName('information_schema')}.{$this->quoteTableColumnName('tables')} WHERE {$this->quoteTableColumnName('table_schema')} = :sDatabaseName AND {$this->quoteTableColumnName('table_name')} = {$this->quoteTableColumnName($this->mTable)};";
		// Setup the statement
		$oTableMeta = $this->mConnection->prepare($sQuery);
		// Set the database name
		$oTableMeta->bindParam(':sDatabaseName', $this->mDatabase);
		// Execute the statement
		$oTableMeta->execute();
		// Return the results
		return $oTableMeta->fetchAll(PDO::FETCH_OBJ);
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the database name into the system
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access public
	 * @param string $sDatabase
	 * @return FramsieDatabaseInterface $this
	 */
	public function setDatabase($sDatabase) {
		// Set the database into the system
		$this->mDatabase = (string) $sDatabase;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the fields for the query into the system all at once
	 * @package FramsieDatabaseInterface
	 * @subpackage Getters
	 * @access public
	 * @param array $aFields
	 * @return FramsieDatabaseInterface $this
	 */
	public function setFields(array $aFields) {
		// Set the fields into the system
		$this->mFields = $aFields;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the interface (the database architecture) into the system
	 * @package Framsie
	 * @subpackage FramsieDatabaseInterface
	 * @access public
	 * @param integer $iInterface
	 * @return FramsieDatabaseInterface $this
	 */
	public function setInterface($iInterface) {
		// Set the interface type into the system
		$this->mInterface = (integer) $iInterface;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the record LIMIT into the system
	 * @package FramsieDatabaseInterface
	 * @subpackage Setters
	 * @access public
	 * @param integer $iMaximum
	 * @param integer [$iMinimum]
	 * @return FramsieDatabaseInterface $this
	 */
	public function setLimit($iMaximum, $iMinimum = null) {
		// Are we trying to set a range
		if (empty($iMinimum)) { // No range
			// Add the LIMIT
			array_push($this->mLimits, $iMaximum);
		} else {                // We have a rangle
			// Add the LIMIT
			array_push($this->mLimits, $iMinimum);
			array_push($this->mLimits, $iMaximum);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the query type into the system using the class' constants
	 * @package FramsieDatabaseInterface
	 * @subpackage Setters
	 * @access public
	 * @param string $sQuery
	 * @return FramsieDatabaseInterface $this
	 */
	public function setQuery($sQuery) {
		// Set the query into the system
		$this->mQuery = (string) $sQuery;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the table name into the system
	 * @package FramsieDatabaseInterface
	 * @subpackage Setters
	 * @access public
	 * @param string $sTableName
	 * @return FramsieDatabaseInterface $this
	 */
	public function setTable($sTableName) {
		// Set the table name into the system
		$this->mTable = (string) $sTableName;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the table alias name into the system
	 * @package FramsieDatabaseInterface
	 * @subpackage Setters
	 * @access public
	 * @param string $sTableAlias
	 * @return FramsieDatabaseInterface $this
	 */
	public function setTableAlias($sTableAlias) {
		// Set the table alias name into the system
		$this->mTableAlias = (string) $sTableAlias;
		// Return the instance
		return $this;
	}
}
