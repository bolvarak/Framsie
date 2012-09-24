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
	 * This property contains an array of databases and their locations
	 * @access protected
	 * @var array
	 */
	protected $mDatabases     = array();

	/**
	 * This property contains an array of indices and the databases and tables they are associated with
	 * @access protected
	 * @var array
	 */
	protected $mIndices       = array();

	/**
	 * This property contains an array of tables and their fields
	 * @access protected
	 * @var array
	 */
	protected $mTables        = array();

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////


	public function __construct() {
		// Return the instance
		return $this;
	}
}