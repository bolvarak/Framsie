<?php
/**
 * This class provides an interface into flat files as well as flat file indexing
 * @package Framsie
 * @subpackage FramsieFlatFileInterface
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieFlatFileInterface {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant defines the separator for CSV files
	 * @var string
	 */
	const SEPARATOR_COMMA   = ',';

	/**
	 * This constant defines the separator for Framsie index and meta files
	 * @var string
	 */
	const SEPARATOR_FRAMSIE = '}::{';

	/**
	 * This constant defines the separator for hash delimited files
	 * @var string
	 */
	const SEPARATOR_HASH    = '#';

	/**
	 * This constant defines the separator for pipe delimited files
	 * @var string
	 */
	const SEPARATOR_PIPE    = '|';

	/**
	 * This constant defines the separator for TSV files
	 * @var string
	 */
	const SEPARATOR_TAB     = '\t';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the current database being used
	 * @access protected
	 * @var string
	 */
	protected $mCurrentDatabase   = null;

	/**
	 * This property contains an array of databases and their locations
	 * @access protected
	 * @var array
	 */
	protected $mDatabases         = array();

	/**
	 * This property contains an instance of FramsieEncryption
	 * @var FramsieEncryption
	 */
	protected $mFramsieEncryption = null;

	/**
	 * This property contains an array of indices and the databases and tables they are associated with
	 * @access protected
	 * @var array
	 */
	protected $mIndices           = array();

	/**
	 * This property contains an array of tables and their fields
	 * @access protected
	 * @var array
	 */
	protected $mTables            = array();

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * For now this constructor simply returns the instance, I'm sure we'll
	 * think of something awesome to do with it later
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access public
	 * @return FramsieFlatFileInterface $this
	 */
	public function __construct() {
		// Instantiate the encryption module
		$this->mFramsieEncryption = new FramsieEncryption();
		// Set the number of passes for this module
		$this->mFramsieEncryption->setPasses(50);
		// Load the databases from the filesystem
		$this->loadDatabaseSystem();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////

	/**
	 * This method creates the database folder on the file system
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sPath
	 * @return FramsieFlatFileInterface $this
	 */
	protected function createFolder($sDatabase) {
		// Create the folder
		mkdir(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase, 0755, true);
		// Return the instance
		return $this;
	}

	/**
	 * This method creates a database's index file
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @return FramsieFlatFileInterface $this
	 */
	protected function createIndexFile($sDatabase) {
		// Create the index file
		$rIndexFile = fopen(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.'Indices.db', 'w');
		// Close the file as we do not need it right now
		fclose($rIndexFile);
		// Return the instance
		return $this;
	}

	/**
	 * This method creates a database's meta file
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @return FramsieFlatFileInterface $this
	 */
	protected function createMetaFile($sDatabase) {
		// Create the meta file
		$rMetaFile = fopen(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.'.meta.db', 'w');
		// Close the file as we do not need it right now
		fclose($rMetaFile);
		// Return the instance
		return $this;
	}

	/**
	 * This method creates a table's db file
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @return FramsieFlatFileInterface $this
	 */
	protected function createTableFile($sDatabase, $sTable) {
		// Create the table file
		$rTableFile = fopen(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.$sTable.'.db', 'w');
		// Close the file as we do not need it right now
		fclose($rTableFile);
		// Return the instnance
		return $this;
	}

	/**
	 * This method decrypts, separates and converts rows to their true PHP value
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param string $sRow
	 * @param array $aFields
	 * @param boolean $bIsEncrypted
	 * @return array
	 */
	protected function decodeRow($sDatabase, $sTable, $sRow, $aFields = array(), $bIsEncrypted = true) {
		// Verify
		$this->verifyDatabase($sDatabase)           // Database
			->verifyTable    ($sDatabase, $sTable); // Table
		// First off remove the EOL notation
		$sRow = (string) str_replace(PHP_EOL, null, $sRow);
		// Determine if we need to decrypt the row or not
		if (($this->getDatabaseMetaProperty('bIsEncrypted', $sDatabase) === true) && ($bIsEncrypted === true)) {
			// Set the hash into the encryption instance
			$this->mFramsieEncryption->setCipher($this->getDatabaseMetaProperty('sHash', $sDatabase));
			// Decrypt the row
			$sRow = (string) $this->mFramsieEncryption->Decrypt($sRow);
		}
		// Filter the row
		$aColumns = $this->filterRow(explode($this->getDatabaseMetaProperty('sSeparator', $sDatabase), $sRow));
		// Create the return array placeholder
		$aReturn  = array();
		// Loop through the fields in the table
		foreach ($this->mTables[$sDatabase][$sTable] as $sName => $iIndex) {
			// Check to see if the field should be added
			if (((empty($aFields) === false) &&  in_array($sName, $aFields)) || ($aFields === '*')) {
				// Set the column into the array
				$aReturn[$sName] = $aColumns[$iIndex];
			}
		}
		// Return the columns
		return new FramsieFlatFileRecord($aReturn);
	}

	/**
	 * This method determines the insert value of a field
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param multitype $mValue
	 * @return string
	 */
	protected function determineInsertValue($mValue) {
		// Check for an array
		if (is_array($mValue)) {
			// Set and return the value
			return "array(".serialize($mValue).")";
		}
		// Check for a boolean
		if (is_bool($mValue)) {
			// Set and return the value
			return (($mValue === true) ? 'true' : 'false');
		}
		// Check for a null
		if (is_null($mValue) || empty($mValue)) {
			// Set and return the value
			return (string) 'NULL';
		}
		// Check for an object
		if (is_object($mValue)) {
			// Set and return the value
			return "object(".serialize($mValue).")";
		}
		// By default simply return the variable
		return (string) $mValue;
	}

	/**
	 * This method determines a field's true returns type, or it's true PHP variable type
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sValue
	 * @return array|boolean|float|integer|object|string
	 */
	protected function determineReturnValue($sValue) {
		// Check for an array
		if (preg_match('/^array\((.*)\)$/i', $sValue, $aArrayMatches)) {
			// Unserialize and return the array
			return unserialize($aArrayMatches[1]);
		}
		// Check the variable type
		if (is_bool($sValue) || preg_match('/^false|true$/', $sValue)) {
			// Return the variable as a boolean
			return (boolean) (($sValue === 'true') ? true : false);
		}
		if (is_float($sValue) || preg_match('/^\d+\.\d+$/', $sValue)) {
			// Return the variable as a floating point
			return (float) $sValue;
		}
		if (is_int($sValue) || preg_match('/^\d+$/', $sValue)) {
			// Return the variable as an integer
			return (integer) $sValue;
		}
		// Check for null
		if (is_null($sValue) || preg_match('/^null|nil$/i', $sValue)) {
			// Return the variable as a null
			return null;
		}
		// Check for an object
		if (preg_match('/^object\((.*)\)$/i', $sValue, $aObjectMatches)) {
			// Unserialize and return the object
			return unserialize($aObjectMatches[1]);
		}
		// By default return a string
		return (string) $sValue;
	}

	/**
	 * This method takes a database row and determines each of the columns return value
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param array $aRow
	 */
	protected function filterRow($aRow) {
		// Return the filtered row
		return array_map(array($this, 'determineReturnValue'), $aRow);
	}

	/**
	 * This method generates a somewhat random encryption hash to use for the database's encryption
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @return string
	 */
	protected function generateEncryptionHash() {
		// Return the encryption hash
		return base64_encode(sha1(microtime().uniqid().time(), uniqid().time().microtime()));
	}

	/**
	 * This method runs a batch set of index searches
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param string $aFields
	 * @param string $aValues
	 * @return array
	 */
	protected function loadBatchIndexLineNumber($sDatabase, $sTable, $aFields, $aValues) {
		// Create an array for the lines
		$aLines = array();
		// Loop through the keys
		for ($iKey = 0; $iKey < count($aFields); $iKey++) {
			// Load the lines
			$aIndexLines = $this->loadIndexLineNumber($sDatabase, $sTable, $aFields[$iKey], $aValues[$iKey]);
			// Check for no lines
			if (empty($aIndexLines) === false) {
				// Merge the arrays
				$aLines = array_merge($aLines, $aIndexLines);
			}
		}
		// Return an array of unique line numbers
		return array_unique($aLines, SORT_NUMERIC);
	}

	/**
	 * This method loads the databases into the system
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @return FramsieFlatFileInterface $this
	 */
	protected function loadDatabaseSystem() {
		// Open and scan the DB container
		$aDatabases = scandir(FLAT_FILE_DB_PATH);
		// Remove the parent and relative paths
		array_shift($aDatabases); // Relative path
		array_shift($aDatabases); // Parent path
		// Loop through the container contents
		foreach ($aDatabases as $sDatabase) {
			// Check to see if the current entity is a directory
			if (is_dir(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase) === true) {
				// Load the meta file
				$aDatabase = $this->loadMetaFile($sDatabase);
				// Remove the hash as it's a secret
				unset($aDatabase['sHash']);
				// Check for indices
				if (empty($aDatabase['aIndices']) === false) {
					// Set the indices
					$this->mIndices[$sDatabase] = $aDatabase['aIndices'];
					// Remove the indices from the database array
					unset($aDatabase['aIndices']);
				}
				// Check for tables
				if (empty($aDatabase['aTables']) === false) {
					// Set the tables
					$this->mTables[$sDatabase] = $aDatabase['aTables'];
					// Remove the tables from the database array
					unset($aDatabase['aTables']);
				}
				// Add the last of the meta data to the global database array
				$this->mDatabases[$sDatabase] = $aDatabase;
			}
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method grabs the line number from the indices file for quick record searching
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param string $sField
	 * @param multitype $mValue
	 * @return array|boolean
	 */
	protected function loadIndexLineNumber($sDatabase, $sTable, $sField, $mValue) {
		// Verify the database
		$this->verifyDatabase($sDatabase)                    // Database
			->verifyTable    ($sDatabase, $sTable);          // Table
		// Check to see if the index exists
		if ($this->verifyIndex($sDatabase, $sTable, $sField, false) === false) {
			// We're done
			return false;
		}
		// Open the index file for reading
		$rIndexFile = fopen(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.'Indices.db', 'r');
		// Create a lines placeholder
		$aLines     = array();
		// Loop through the file lines
		while (!feof($rIndexFile)) {
			// Grab the line
			$sLine = (string) fgets($rIndexFile);
			// Determine if we need to decrypt the line
			if ($this->getDatabaseMetaProperty('bIsEncrypted', $sDatabase) === true) {
				// Set the hash into the encryption instance
				$this->mFramsieEncryption->setCipher($this->getDatabaseMetaProperty('sHash', $sDatabase));
				// Decrypt the line
				$sLine = (string) $this->mFramsieEncryption->Decrypt($sLine);
			}
			// Separate the columns and filter them
			$aColumns = $this->filterRow(explode($this->getDatabaseMetaProperty('sSeparator', $sDatabase), $sLine));
			// Check for the index
			if (($aColumns[0] === $sTable) && ($aColumns[1] === $sField) && ($aColumns[2] === $mValue)) {
				// Add the line number
				array_push($aLines, $aColumns[3]);
			}
		}
		// Return
		return (empty($aLines) ? false : array_unique($aLines, SORT_NUMERIC));
	}

	/**
	 * This method loads a database meta file into the system
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @throws Exception
	 * @return array
	 */
	protected function loadMetaFile($sDatabase) {
		// Make sure the file exists
		$this->verifyDatabaseMetaFile($sDatabase);
		// Load the meta file
		$aDatabase = json_decode(file_get_contents(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.'.meta.db'), true);
		// Check to see if the data is present
		if (empty($aDatabase)) {
			// Throw an exception
			throw new Exception("Unable to load \"{$sDatabase}\" meta file.");
		}
		// Return the database meta file
		return $aDatabase;
	}


	/**
	 * This method searches a tables that has no indexed fields or none that were specified
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param array $aWhereColumns
	 * @param array $aWhereValues
	 * @return array
	 */
	protected function loadNoIndexSearchResults($sDatabase, $sTable, $aWhereColumns, $aWhereValues) {
		// Verify
		$this->verifyDatabase($sDatabase)          // Database
			->verifyTable   ($sDatabase, $sTable); // Table
		// Setup a lines placeholder
		$aLines = array();
		// Open the table file
		$rTableFile = fopen(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.$sTable.'.db', 'r');
		// Loop through the file lines
		while (!feof($rTableFile)) {
			// Set the current line
			$sLine = fgets($rTableFile);
			// Determine if we need to decrypt the line or not
			if ($this->getDatabaseMetaProperty('bIsEncrypted', $sDatabase) === true) {
				// Set the hash into the encryption instance
				$this->mFramsieEncryption->setCipher($this->getDatabaseMetaProperty('sHash', $sDatabase));
				// Decrypt the line
				$sLine = $this->mFramsieEncryption->Decrypt($sLine);
			}
			// Separate and split the row
			$aRow   = $this->filterRow(explode($this->getDatabaseMetaProperty('sSeparator', $sDatabase), $sLine));
			// Setup a match notification
			$bMatch = true;
			// Loop through the where columns
			for ($iKey = 0; $iKey < count($aWhereColumns); $iKey++) {
				// Check the row
				if ($aRow[$this->mTables[$sDatabase][$sTable][$aWhereColumns[$iKey]]] !== $aWhereValues[$iKey]) {
					// Set the match notification
					$bMatch = (boolean) false;
				}
			}
			// Check the notification
			if ($bMatch === true) {
				// Add the line
				array_push($aLines, $sLine);
			}
		}
		// Return the lines
		return $aLines;
	}

	/**
	 * This method looks for a specific line number in a file and returns the row at that line
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param integer $iDesiredLine
	 * @return boolean|string
	 */
	protected function loadRowByLineNumber($sDatabase, $sTable, $iDesiredLine) {
		// Verify
		$this->verifyDatabase($sDatabase)           // Database
			->verifyTable    ($sDatabase, $sTable); // Table
		// Open the table file
		$rTableFile = fopen(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.$sTable.'.db', 'r');
		// Create a line iterator
		$iLine      = (integer) 0;
		// Loop through the lines
		while (!feof($rTableFile)) {
			// Set the current line
			$sLine = fgets($rTableFile);
			// Set the line count
			$iLine = (integer) ($iLine + substr_count($sLine, PHP_EOL));
			// Check to see if this is the property line
			if ($iLine === $iDesiredLine) {
				// Close the file handle
				fclose($rTableFile);
				// Return the line
				return $sLine;
			}
		}
		// If we get to this point, the line number is invalid
		return false;
	}

	/**
	 * This method replaces any illegal characters with an underscore
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sEntity
	 * @return string
	 */
	protected function removeIllegalCharacters($sEntity) {
		// Return the validated string
		return preg_replace('/[^a-zA-Z0-9_]+/', '_', $sEntity);
	}

	/**
	 * This method ensures that a database has been selected for use
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @throws Exception
	 * @return FramsieFlatFileInterface $this
	 */
	protected function verifyCurrentDatabase() {
		// Check to see if the current database has been set
		if (empty($this->mCurrentDatabase)) {
			// Throw an exception
			throw new Exception("No current database has been set.");
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method ensures that the database exists in the system
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @throws Exception
	 * @return FramsieFlatFileInterface $this
	 */
	protected function verifyDatabase($sDatabase) {
		// Make sure the database exists
		if (empty($this->mDatabases[$sDatabase])) {
			// The database doesn't exist, throw an exception
			throw new Exception("The database \"{$sDatabase}\" does not exist in the system.");
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method ensures that a database's folder container exists on the filesystem
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @return FramsieFlatFileInterface $this
	 */
	protected function verifyDatabaseFolder($sDatabase) {
		// Check for the database folder
		if (file_exists(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase) === false) {
			// Create the folder
			$this->createFolder($sDatabase);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method ensures that a database's index file exists on the filesystem
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDataase
	 * @return FramsieFlatFileInterface $this
	 */
	protected function verifyDatabaseIndexFile($sDatabase) {
		// Check for the index file
		if (file_exists(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.'indices.db') === false) {
			// Create the index file
			$this->createIndexFile($sDatabase);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method ensures that a database's meta file exists on the filesystem
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @return FramsieFlatFileInterface $this
	 */
	protected function verifyDatabaseMetaFile($sDatabase) {
		// Check for the meta file
		if (file_exists(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.'.meta.db') === false) {
			// Create the meta file
			$this->createMetaFile($sDatabase);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method ensures that a field exists in a table
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param string $sField
	 * @throws Exception
	 * @return FramsieFlatFileInterface $this
	 */
	protected function verifyField($sDatabase, $sTable, $sField) {
		// Check to see if the field exists
		if (empty($this->mTables[$sDatabase][$sTable][$sField]) && (is_null($this->mTables[$sDatabase][$sTable][$sField]) === true)) {
			// The field does not exist, throw an exception
			throw new Exception("The field \"{$sDatabase}.{$sTable}.{$sField}\" does not exist.");
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method runs a check to see if the index exists
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param string $sField
	 * @param boolean $bThrowException
	 * @throws Exception
	 * @return boolean
	 */
	protected function verifyIndex($sDatabase, $sTable, $sField, $bThrowException = true) {
		// Check to see if the index field exists
		if (in_array($sField, $this->mIndices[$sDatabase][$sTable])) {
			// We're done
			return true;
		}
		// Check to see if we need to throw an exception
		if ($bThrowException === true) {
			// Throw the exception
			throw new Exception("The index \"{$sDatabase}.{$sTable}[{$sField}]\" does not exist.");
		}
		// Elsewise, return false
		return false;
	}

	/**
	 * This method ensures the table exists for the database
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @throws Exception
	 * @return FramsieFlatFileInterface $this
	 */
	protected function verifyTable($sDatabase, $sTable) {
		// Make sure the table exists
		if (empty($this->mTables[$sDatabase][$sTable]) && (isset($this->mTables[$sDatabase][$sTable]) === false)) {
			// The table doesn't exist, throw an exception
			throw new Exception("The table \"{$sDatabase}.{$sTable}\" does not exist.");
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method ensures that a table's db file exists on the filesystem
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @return FramsieFlatFileInterface $this
	 */
	protected function verifyTableFile($sDatabase, $sTable) {
		// Check for the table file
		if (file_exists(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.$sTable.'.db') === false) {
			// Create the table file
			$this->createTableFile($sDatabase, $sTable);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method writes an index value to the index file
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param string $sField
	 * @param string $sValue
	 * @param integer $iLine
	 * @return FramsieFlatFileInterface $this
	 */
	protected function writeIndexValue($sDatabase, $sTable, $sField, $sValue, $iLine) {
		// Verify the database details
		$this->verifyDatabase($sDatabase)
			->verifyTable    ($sDatabase, $sTable)
			->verifyField    ($sDatabase, $sTable, $sField);
		// Make sure the field is in the indices
		if ($this->verifyIndex($sDatabase, $sTable, $sField, false)) {
			// Open the indices file for appending
			$rIndexFile = fopen(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.'Indices.db', 'a');
			// Create the row
			$sRow = (string) implode($this->getDatabaseMetaProperty('sSeparator', $sDatabase), array(
				$sTable,
				$sField,
				$sValue,
				$iLine
			));
			// Determine if we need to encrypt the row
			if ($this->getDatabaseMetaProperty('bIsEncrypted', $sDatabase) === true) {
				// Set the hash into the encryption instance
				$this->mFramsieEncryption->setCipher($this->getDatabaseMetaProperty('sHash', $sDatabase));
				// Encrypt the row
				$sRow = (string) $this->mFramsieEncryption->Encrypt($sRow);
			}
			// Write the index line
			fwrite($rIndexFile, $sRow.PHP_EOL, strlen($sRow.PHP_EOL));
			// Close the file
			fclose($rIndexFile);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method writes the database's meta data to the file system
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @return FramsieFlatFileInterface $this
	 */
	protected function writeMetaData($sDatabase) {
		// Make sure the database exists
		$this->verifyDatabase       ($sDatabase)  // The datbase itself
			->verifyDatabaseFolder  ($sDatabase)  // The database container
			->verifyDatabaseMetaFile($sDatabase); // The database meta file
		// Localize the database data
		$aDatabase = $this->mDatabases[$sDatabase];
		// Try to get the file's contents
		$aStoredDatabase = json_decode(file_get_contents(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.'.meta.db'), true);
		// Check the load attempt
		if (is_null($aStoredDatabase) === false && ($aDatabase['bIsEncrypted'] === true)) {
			// Set the encryption hash on the table to its original state
			$this->mDatabases[$sDatabase]['sHash'] = $aStoredDatabase['sHash']; // Globalized
			$aDatabase['sHash']                    = $aStoredDatabase['sHash']; // Localized
		}
		// Check to see if the tables have been set
		if (empty($this->mTables[$sDatabase]) === false) {
			// Add the tables to the local database data
			$aDatabase['aTables'] = $this->mTables[$sDatabase];
		}
		// Check to see if the indices have been set
		if (empty($this->mIndices[$sDatabase]) === false) {
			// Add the indices to the local database data
			$aDatabase['aIndices'] = $this->mIndices[$sDatabase];
		}
		// Open the meta file
		$rMetaFile = fopen(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.'.meta.db', 'w');
		// Write the meta data
		fwrite($rMetaFile, json_encode($aDatabase), strlen(json_encode($aDatabase)));
		// Close the file
		fclose($rMetaFile);
		// Return the instance
		return $this;
	}

	/**
	 * This method writes a row to the table
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sDatabase
	 * @param string $sTabe
	 * @param string $sRow
	 * @return integer
	 */
	protected function writeTableRow($sDatabase, $sTable, $sRow) {
		// Verify the database, table and table file
		$this->verifyDatabase($sDatabase)
			->verifyTable    ($sDatabase, $sTable)
			->verifyTableFile($sDatabase, $sTable);
		// Determine if we need to encrypt the data or not
		if ($this->getDatabaseMetaProperty('bIsEncrypted', $sDatabase) === true) {
			// Set the cipher to the database's hash
			$this->mFramsieEncryption->setCipher($this->getDatabaseMetaProperty('sHash', $sDatabase));
			// Encrypt the row
			$sRow = (string) $this->mFramsieEncryption->Encrypt($sRow);
		}
		// Open the table file for appending
		$rTableFile = fopen(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.$sTable.'.db', 'a');
		// Write the row
		fwrite($rTableFile, $sRow.PHP_EOL, strlen($sRow.PHP_EOL));
		// Close the file
		fclose($rTableFile);
		// Return the line count
		return $this->getLineCount(FLAT_FILE_DB_PATH.DIRECTORY_SEPARATOR.$sDatabase.DIRECTORY_SEPARATOR.$sTable.'.db');
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds a database to the system
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access public
	 * @param string $sName
	 * @param boolean $bIsEncrypted
	 * @param boolean $bIsJson
	 * @param boolean $bIsXml
	 * @throws Exception
	 * @return FramsieFlatFileInterface
	 */
	public function addDatabase($sName, $bIsEncrypted = true, $sSeparator = self::SEPARATOR_FRAMSIE) {
		// Validate the database name
		$sName = (string) $this->removeIllegalCharacters($sName);
		// Check to see if the database already exists
		if (empty($this->mDatabases[$sName])) {
			// Setup the database
			$this->mDatabases[$sName] = array(
				'bIsEncrypted' => (boolean) $bIsEncrypted,
				'sSeparator'   => (string)  $sSeparator
			);
			// Check to see if the database is encrypted
			if ($bIsEncrypted === true) {
				// Generate the database's hash
				$this->mDatabases[$sName]['sHash'] = (string) $this->generateEncryptionHash();
			}
			// Create the database placeholders
			$this->mTables[$sName]  = array();
			$this->mIndices[$sName] = array();
			// Verify the database files and folders
			$this->verifyDatabaseFolder  ($sName)  // Container
				->verifyDatabaseIndexFile($sName)  // Index File
				->verifyDatabaseMetaFile ($sName); // Meta File
			// Write data to the meta file
			$this->writeMetaData($sName);
		}
		// We're done
		return $this;
	}

	/**
	 * This method adds a field to a database's table
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access public
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param string $sName
	 * @param integer $iPosition
	 * @throws Exception
	 * @return FramsieFlatFileInterface $this
	 */
	public function addField($sDatabase, $sTable, $sName, $iPosition) {
		// Verify the database exists
		$this->verifyDatabase($sDatabase);
		// Verify the table exists
		$this->verifyTable($sDatabase, $sTable);
		// Make sure the name is a string
		if (is_string($sName) === false) {
			// Throw an exception
			throw new Exception("The field \"{$sName}\" should be a string representation of the field position in the file.");
		}
		// Make sure the position is an integer
		if (is_numeric($iPosition) === false) {
			// Throw an exception
			throw new Exception("The field \"{$sName}\" should be the key of a numerical value that represents the the position of the field in the file.");
		}
		// Validate the field name
		$sName = (string) $this->removeIllegalCharacters($sName);
		// Add the field to the table
		$this->mTables[$sDatabase][$sTable][$sName] = (integer) $iPosition;
		// Write the meta data
		$this->writeMetaData($sDatabase);
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a field index to the system
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access public
	 * @param string $sDatabase
	 * @param string $sTable
	 * @param string $sField
	 * @return FramsieFlatFileInterface $this
	 */
	public function addIndex($sDatabase, $sTable, $sField) {
		// Make sure the database exists
		$this->verifyDatabase($sDatabase);
		// Make sure the table exists
		$this->verifyTable($sDatabase, $sTable);
		// Add the index
		array_push($this->mIndices[$sDatabase][$sTable], $sField);
		// Write the database meta data
		$this->writeMetaData($sDatabase);
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a table to a database
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access public
	 * @param string $sDatabase
	 * @param string $sName
	 * @param array $aFields
	 * @param string $sSeparator
	 * @throws Exception
	 * @return FramsieFlatFileInterface $this
	 */
	public function addTable($sDatabase, $sName, $aFields = array(), $sSeparator = self::SEPARATOR_FRAMSIE) {
		// Verify that the database exists
		$this->verifyDatabase($sDatabase);
		// Validate the table name
		$sName = (string) $this->removeIllegalCharacters($sName);
		// Set the table into the system
		$this->mTables[$sDatabase][$sName] = array();
		// Verify the table file
		$this->verifyTableFile($sDatabase, $sName);
		// Setup the placeholders
		$this->mIndices[$sDatabase][$sName] = array();
		// Write the meta data
		$this->writeMetaData($sDatabase);
		// Loop through the fields and make sure they are valid
		foreach ($aFields as $sFieldName => $iPosition) {
			// Add the field
			$this->addField($sDatabase, $sName, $sFieldName, $iPosition);
		}
		// Write the meta data one last time
		$this->writeMetaData($sDatabase);
		// Return the instance
		return $this;
	}

	/**
	 * This method is the public interface into searching the databases
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sTable
	 * @param array $aFields
	 * @param array $aWhere
	 * @return array|FramsieFlatFileRecord
	 */
	public function find($sTable, $aFields, $aWhere = array()) {
		// Verify current database
		$this->verifyCurrentDatabase();
		// Verify the table
		$this->verifyTable($this->mCurrentDatabase, $sTable);
		// Try to grab the line number(s) from the index
		$aLines = $this->loadBatchIndexLineNumber($this->mCurrentDatabase, $sTable, array_keys($aWhere), array_values($aWhere));
		// Check for line numbers
		if (empty($aLines)) {
			// Create a row placeholder
			$aRows = array();
			// Run the search
			foreach ($this->loadNoIndexSearchResults($this->mCurrentDatabase, $sTable, array_keys($aWhere), array_values($aWhere)) as $sRow) {
				// Decode the row and append it
				array_push($aRows, $this->decodeRow($this->mCurrentDatabase, $sTable, $sRow, $aFields, false));
			}
			// Return the array
			return $aRows;
		} else {
			// Check to see if there is more than one line
			if (count($aLines) === 1) {
				// Return the line
				return $this->decodeRow($this->mCurrentDatabase, $sTable, $this->loadRowByLineNumber($this->mCurrentDatabase, $sTable, $aLines[0]), $aFields);
			} else {
				// Create a row placeholder
				$aRows = array();
				// Loop through the lines
				foreach ($aLines as $iLine) {
					// Load the row
					array_push($aRows, $this->decodeRow($this->mCurrentDatabase, $sTable, $this->loadRowByLineNumber($this->mCurrentDatabase, $sTable, $iLine)), $aFields);
				}
				// Return the rows
				return $aRows;
			}
		}
	}

	/**
	 * This method inserts a record into a database and adds the index values if any exist
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access public
	 * @param string $sTable
	 * @param array $aFields
	 * @return FramsieFlatFileInterface $this
	 */
	public function insert($sTable, $aFields) {
		// Verify current database
		$this->verifyCurrentDatabase();
		// Verify the table
		$this->verifyTable($this->mCurrentDatabase, $sTable);
		// Create a record placeholder
		$aRecord = array();
		// Loop through the fields
		foreach ($aFields as $sField => $mValue) {
			// Verify the field
			$this->verifyField($this->mCurrentDatabase, $sTable, $sField);
			// Set the record into the array
			$aRecord[$this->mTables[$this->mCurrentDatabase][$sTable][$sField]] = $this->determineInsertValue($mValue);
		}
		// Write the table row
		$iLine = (integer) $this->writeTableRow($this->mCurrentDatabase, $sTable, implode($this->getDatabaseMetaProperty('sSeparator'), $aRecord));
		// Loop through the fields once more
		foreach ($aFields as $sField => $mValue) {
			// Verify the field
			$this->verifyField($this->mCurrentDatabase, $sTable, $sField);
			// Write the index
			// Write the index
			$this->writeIndexValue($this->mCurrentDatabase, $sTable, $sField, $this->determineInsertValue($mValue), $iLine);
		}
		// We're done, return the instance
		return $this;
	}


	public function update($sTable, $aFields, $aWhere) {

	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Getters ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method loads properties from database meta files
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @param string $sProperty
	 * @param string $sDatabase
	 * @throws Exception
	 * @return multitype
	 */
	protected function getDatabaseMetaProperty($sProperty, $sDatabase = null) {
		// Check to see if we need to verify the current database
		if (empty($sDatabase)) {
			// Verify the current database
			$this->verifyCurrentDatabase();
			// Set the database
			$sDatabase = $this->mCurrentDatabase;
		} else {
			// Verify the database
			$this->verifyDatabase       ($sDatabase)
				->verifyDatabaseMetaFile($sDatabase);
		}
		// Load the meta file
		$aDatabase = $this->loadMetaFile($sDatabase);
		// Check to see if the property exists
		if (empty($aDatabase[$sProperty]) && is_null($aDatabase[$sProperty])) {
			// Throw an exception
			throw new Exception("Property \"{$sProperty}\" does not exist for database \"{$sDatabase}.\"");
		}
		// Return the property
		return $aDatabase[$sProperty];
	}

	/**
	 * This method retrieves the true line count of a table file as the last line is PHP_EOL
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access protected
	 * @param string $sFile
	 * @throws Exception
	 * @return integer
	 */
	protected function getLineCount($sFile) {
		// Make sure the filename exists
		if (file_exists($sFile) === false) {
			// Throw an exception
			throw new Exception("The file \"{$sFile}\" does not exist on the filesystem.");
		}
		// Create a line count iterator
		$iLines = (integer) 0;
		// Open the file
		$rFileHandle = fopen($sFile, 'r');
		// Loop through the lines
		while (!feof($rFileHandle)) {
			// Set the current line
			$sLine = fgets($rFileHandle, 4096);
			// Set the line count
			$iLines = (integer) ($iLines + substr_count($sLine, PHP_EOL));
		}
		// Close the file
		fclose($rFileHandle);
		// Return the line count
		return $iLines;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the database that is currently being used
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access public
	 * @return string
	 */
	public function getCurrentDatabase() {
		// Return the current database
		return $this->mCurrentDatabase;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method tells the system which database to use by default
	 * @package Framsie
	 * @subpackage FramsieFlatFileInterface
	 * @access public
	 * @param string $sDatabase
	 * @return FramsieFlatFileInterface $this
	 */
	public function setCurrentDatabase($sDatabase) {
		// Make sure the database exists
		$this->verifyDatabase($sDatabase);
		// Set the current database
		$this->mCurrentDatabase = (string) $sDatabase;
		// Return the instance
		return $this;
	}
}
