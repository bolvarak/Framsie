<?php
/**
 * This class provides easy singletons for models
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieModel {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of this class
	 * @access protected
	 * @staticvar FramsieModel
	 */
	protected static $mInstance   = null;

	/**
	 * This property contains the mapper associated with the model
	 * @access protected
	 * @var FramsieMapper
	 */
	protected $mMapper            = null;
	
	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method creates a singleton instance out of this class or its child
	 * @package Framsie
	 * @subpackage FramsieModel
	 * @access public
	 * @static
	 * @return Framsie::Singleton
	 */
	public static function getInstance($bReset = false) {
		// Set the class
		$sClass    = get_called_class();
		// Check for a reset notification
		if ($bReset === true) {
			// Return the new singleton
			return Framsie::SingletonReset($sClass);
		}
		// Return the framsie singleton
		return Framsie::Singleton($sClass);
	}
	
	/**
	 * This method sets an external instance into the class
	 * @package Framsie
	 * @subpackage FramsieModel
	 * @access public
	 * @static
	 * @throws FramsieException
	 * @return FramsieModel self::$mInstance
	 */
	public static function setInstance($oInstance) {
		// Determine the called class
		$sClass = get_called_class();
		// Make sure we have an instance of determined class
		if ($oInstance instanceof $sClass) {
			// Set the new instance
			self::$mInstance = $oInstance;
			// Return the instance
			return self::$mInstance;
		}
		// Trigger an Exception
		FramsieError::Trigger('FRAMIIN', array($sClass));
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method loads all of the records in a table into their mappers
	 * @package Framsie
	 * @subpackage FramsieModel
	 * @access protected
	 * @param string $sTable
	 * @param string $sPrimaryKey
	 * @param string $sMapper
	 * @param array $aWhere
	 * @param integer $iLimit
	 * @return array
	 */
	protected function loadTableMaps($sTable, $sPrimaryKey, $sMapper, $aWhere = array(), $iLimit = 0) {
		// Create the return array placeholder
		$aReturn = array();
		// Setup the DBI
		FramsieDatabaseInterface::getInstance(true)
			->setTable($sTable)
			->setQuery(FramsieDatabaseInterface::SELECTQUERY)
			->addField($sPrimaryKey);
		// Load the WHERE clauses
		foreach ($aWhere as $sColumn => $mValue) {
			// Add the WHERE clause
			FramsieDatabaseInterface::getInstance()->addWhereClause($sColumn, $mValue);
		}
		// Check for a limit
		if (empty($iLimit) === false) {
			// Set the limit
			FramsieDatabaseInterface::getInstance()->setLimit($iLimit);
		}
		// Loop through the results
		foreach (FramsieDatabaseInterface::getInstance()->generateQuery()->getRows(PDO::FETCH_OBJ) as $oRow) {
			// Create the mapper
			$oMapper = new $sMapper();
			// Load the record into the map
			array_push($aReturn, $oMapper->load($oRow->{$sPrimaryKey}));
		}
		// Return the array
		return $aReturn;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current mapper instance
	 * @package Framsie
	 * @subpackage FramsieModel
	 * @access public
	 * @return FramsieMapper
	 */
	public function getMapper() {
		// Return the mapper
		return $this->mMapper;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the mapper instance into the class
	 * @package Framsie
	 * @subpackage FramsieModel
	 * @access public
	 * @param FramsieMapper $oInstance
	 * @return FramsieModel $this
	 */
	public function setMapper(FramsieMapper $oInstance) {
		// Set the mapper into the class
		$this->mMapper = $oInstance;
		// Return the instance
		return $this;
	}
}
