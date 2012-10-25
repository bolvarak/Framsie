<?php
/**
 * This class provides caching services for Framsie
 * and processes said request object to run the application in a MVC pattern
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieCache {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the number of seconds for a 30 second expire
	 * @var integer
	 */
	const EXPIRE_30_SEC     = 30;

	/**
	 * This constant contains the number of seconds for a 1 minute expire
	 * @var integer
	 */
	const EXPIRE_1_MIN      = 60;

	/**
	 * This constant contains the number of seconds for a 5 minute expire
	 * @var integer
	 */
	const EXPIRE_5_MIN      = 300;

	/**
	 * This constant contains the number of seconds for a 10 minute expire
	 * @var integer
	 */
	const EXPIRE_10_MIN     = 600;

	/**
	 * This constant contains the number of seconds for a 30 minute expire
	 * @var integer
	 */
	const EXPIRE_30_MIN     = 1800;

	/**
	 * This constant contains the number of seconds for a 1 hour expire
	 * @var integer
	 */
	const EXPIRE_1_HOUR     = 3600;

	/**
	 * This constant contains the number of seconds for a 1 day expire
	 * @var integer
	 */
	const EXPIRE_1_DAY      = 86400;

	/**
	 * This constant contains the number of seconds for a 1 business week expire
	 * @var integer
	 */
	const EXPIRE_1_BUS_WEEK = 432000;

	/**
	 * This constant contains the number of seconds for a 1 calendar week expire
	 * @var integer
	 */
	const EXPIRE_1_CAL_WEEK = 604800;

	/**
	 * This constant contains the number of seconds for a 1 month expire
	 * @var integer
	 */
	const EXPIRE_1_MONTH    = 2592000;

	/**
	 * This constant contains the number of seconds for a 1 year expire
	 * @var integer
	 */
	const EXPIRE_1_YEAR     = 31536000;

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instane of the class
	 * @access protected
	 * @staticvar FramsieCache
	 */
	protected static $mInstance = null;

	/**
	 * This property contains the time until the cache expires, the default
	 * is five minutes
	 * @access protected
	 * @var integer
	 */
	protected $mExpire          = 300;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method provides access to the singleton instance of this class
	 * @package Framsie
	 * @subpackage FramsieCache
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieCache self::$mInstance
	 */
	public static function getInstance($bReset = false) {
		// Check for an existing instance or a reset notification
		if (empty(self::$mInstance) || ($bReset === true)) {
			// Create a new instance
			self::$mInstance = new self();
		}
		// Return the instance
		return self::$mInstance;
	}

	/**
	 * This method sets an external instance into the class, it is generally
	 * only used in testing and primarily with phpUnit
	 * @package Framsie
	 * @subpackage FramsieCache
	 * @access public
	 * @static
	 * @param FramsieCache $oInstance
	 * @return FramsieCache self::$mInstance
	 */
	public static function setInstance(FramsieCache $oInstance) {
		// Set the external instance
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply returns the instance of the class
	 * @package Framsie
	 * @subpackage FramsieCache
	 * @access public
	 * @return FramsieCache $this
	 */
	public function __construct() {
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method checks the cache storage to see if a particular asset exists
	 * @package Framsie
	 * @subpackage FramsieCache
	 * @access public
	 * @param string $sName
	 * @return boolean
	 */
	public function cacheExists($sName) {
		// Set the cache filename
		$sCacheFilename = (string) CACHE_DIRECTORY.DIRECTORY_SEPARATOR.$sName.'.cache';
		// Set the cache file info container
		$sInfoFilename  = (string) CACHE_DIRECTORY.DIRECTORY_SEPARATOR.$sName.'.info';
		// Make sure the cache directory exists
		if (!file_exists(CACHE_DIRECTORY)) {
			// Throw an exception because this directory must exist in order to
			// utilize caching throughout the framework
			FramsieError::Trigger('FRAMCAC', array(CACHE_DIRECTORY));
		}
		// Now make sure the cache and info file exists
		if (!file_exists($sCacheFilename) || !file_exists($sInfoFilename)) {
			// No cache is available so return
			return false;
		}
		// Load the cache file contents
		$sContent     = (string) file_get_contents($sCacheFilename);
		// Load the cache file info container contents
		$aInformation = (array) json_decode(file_get_contents($sInfoFilename), true);
		// Check to see if the cache file is valid
		if (($aInformation['iTime'] + $this->mExpire) >= time()) {
			// The cache is valid, we're done
			return true;
		}
		// Cache was present, but has expired
		return false;
	}

	/**
	 * This method loads a cache file from the system, validates it and returns
	 * its content if validation is successful
	 * @package Framsie
	 * @subpackage FramsieCache
	 * @access public
	 * @param string $sName
	 * @throws FramsieException
	 * @return multitype
	 */
	public function loadFromCache($sName) {
		// Check to see if cache even exists
		if ($this->cacheExists($sName) === false) {
			// Nothing to do, return
			return false;
		}
		// Set the cache filename
		$sCacheFilename = (string) CACHE_DIRECTORY.DIRECTORY_SEPARATOR.$sName.'.cache';
		// Set the cache file info container
		$sInfoFilename  = (string) CACHE_DIRECTORY.DIRECTORY_SEPARATOR.$sName.'.info';
		// Load the cache file contents
		$sContent       = (string) file_get_contents($sCacheFilename);
		// Load the cache file info container contents
		$aInformation   = (array) json_decode(file_get_contents($sInfoFilename), true);
		// Return the cache file contents
		return (($aInformation['bSerialized'] === true) ? unserialize($sContent) : $sContent);
	}

	/**
	 * This method saves a cache file and information file to the system
	 * @package Framsie
	 * @subpackage FramsieCache
	 * @access public
	 * @param string $sName
	 * @param multitype $sContent
	 * @throws FramsieException
	 * @return FramsieCache $this
	 */
	public function saveToCache($sName, $sContent) {
		// Define an information array
		$aInformation   = array(
			'bSerialized' => (boolean) false,
			'iTime'       => (integer) time()
		);
		// Make sure the cache directory exists
		if (!file_exists(CACHE_DIRECTORY)) {
			// Throw an exception, because we need this directore in order
			// to utilize caching in the framework
			FramsieError::Trigger('FRAMCAC', array(CACHE_DIRECTORY));
		}
		// Set the name of the cache file
		$sCacheFilename = (string) CACHE_DIRECTORY.DIRECTORY_SEPARATOR.$sName.'.cache';
		// Set the name of the cache file info container
		$sInfoFilename  = (string) CACHE_DIRECTORY.DIRECTORY_SEPARATOR.$sName.'.info';
		// Check the content type for php specific data
		if (is_array($sContent) || is_object($sContent)) {
			// Serialize the content
			$sContent = (string) serialize($sContent);
			// Set the serialized key in the information array
			$aInformation['bSerialized'] = (boolean) true;
		}
		// Save the content to the cache file
		file_put_contents($sCacheFilename, $sContent);
		// Save the information to the info file
		file_put_contents($sInfoFilename, json_encode($aInformation));
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current number of second until a cache file
	 * should expire and become invalid
	 * @package Framsie
	 * @subpackage FramsieCache
	 * @access public
	 * @return integer
	 */
	public function getExpire() {
		// Return the current expire time
		return $this->mExpire;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the number of seconds until the cache should expire
	 * @package Framsie
	 * @subpackage FramsieCache
	 * @access public
	 * @param integer $iSeconds
	 * @return FramsieCache $this
	 */
	public function setExpire($iSeconds) {
		// Set the cache expire time in seconds
		$this->mExpire = (integer) $iSeconds;
		// Return the instance
		return $this;
	}
}
