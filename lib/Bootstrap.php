<?php
/**
 * Start sessions
 */
session_start();
/**
 * This class handles the execution of the application
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class Bootstrap {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property stores our singleton instance
	 * @access protected
	 * @staticvar
	 * @var Bootstrap
	 */
	protected static $mInstance  = null;

	/**
	 * This property stores generated instances of classes
	 * @access protected
	 * @staticvar
	 * @var array
	 */
	protected static $mInstances = array();

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method creates and ensures there is always a single instance
	 * of this class floating around
	 * @package DatabaseService
	 * @static
	 * @access public
	 * @param boolean [$bReset]
	 * @return Bootstrap self::$mInstance
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
	 * @package DatabaseService
	 * @static
	 * @access public
	 * @param Bootstrap $oInstance
	 * @return Bootstrap self::$mInstance
	 */
	public static function setInstance(Bootstrap $oInstance) {
		// Set the new instance
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constructor sets up all of the path definitions and spl handlers,
	 * it's protected to enforce the singleton pattern
	 * @package Bootstrap
	 * @access protected
	 * @return Bootstrap
	 */
	protected function __construct() {
		// Check the application environment
		if (APPLICATION_ENVIRONMENT == 'development') {
			// Turn error reporting on, you can't fix things if you can't see errors
			$this->setErrorReporting(true);
		} else {
			// Turn error reporting off, don't want to give any of our secrets away
			$this->setErrorReporting(false);
		}
		// Define the static file path
		define('STATIC_FILE_PATH',        dirname(__FILE__).'/../public');
		// Define the library path
		define('LIBRARY_PATH',            dirname(__FILE__));
		// Define the Frames path
		define('FRAMSIE_PATH',            LIBRARY_PATH.'/framsie');
		// Define the application path
		define('APPLICATION_PATH',        LIBRARY_PATH.'/../application');
		// Define the model path
		define('MODEL_PATH',              APPLICATION_PATH.'/models');
		// Define the controller path
		define('CONTROLLER_PATH',         APPLICATION_PATH.'/controllers');
		// Define the block path
		define('BLOCK_PATH',              APPLICATION_PATH.'/blocks');
		// Define the JS assets path
		define('JAVASCRIPT_ASSETS_PATH',  'assets/js');
		// Define the CSS assets path
		define('CSS_ASSETS_PATH',         'assets/css');
		// Define the Image assets path
		define('IMG_ASSETS_PATH',         'assets/img');
		// Define the include path
		define('INCLUDE_PATH',            dirname(__FILE__).'/../includes');
		// Define the cache path
		define('CACHE_DIRECTORY',         INCLUDE_PATH.'/cache');
		// Define the configuration path
		define('CONFIGURATION_FILE_PATH', APPLICATION_PATH.'/configs/application.ini');
		// Setup the autoloader
		spl_autoload_register(array($this, 'autoLoader'));
		// Setup the error handler
		// set_error_handler    (array($this, 'dispatchErrors'));
		// Setup the exception handler
		set_exception_handler(array(Framsie::getInstance(), 'dispatchException'));
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method generates a singleton of any existing class passed to it
	 * @param string $sClassName
	 * @param boolean [$bReset]
	 * @throws Eception
	 * @return instanceof $sClassName
	 */
	public static function Instantiate($sClassName, $bReset = false) {
		// First off we check for the existance of the class
		if (class_exists($sClassName)) {
			// Check for the class in the stored array
			if (empty(self::$mInstances[$sClassName]) || ($bReset === true)) {
				// Create a new instance of the class
				self::$mInstances[$sClassName] = new $sClassName();
			}
			// Return the instance
			return self::$mInstances[$sClassName];
		}
		// We're done, something went awry
		throw new Exception("We could not instantiate the class \"{$sClassName}.\"");
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method automagically loads class files (if they exist)
	 * @package Bootstrap
	 * @access public
	 * @param string $sClassName
	 * @throws Exception
	 * @return void
	 */
	public function autoLoader($sClassName) {
		// Check for a Framsie package class
		if ((strpos($sClassName, 'Framsie') !== false) && ($sClassName !== 'Framsie')) {
			// Replace the the class name
			$sClassName = (string) str_replace('Framsie', null, $sClassName);
		}

		// Check for a mapper
		if ((strpos($sClassName, 'Mapper') !== false) && ($sClassName !== 'FramsieMapper')) {
			// Replace the class name
			$sClassName = (string) str_replace('Mapper', null, $sClassName);
			// Set the directory
			$sClassName = (string) "mappers/{$sClassName}";
		}

		// First we check in the library path, so set the filename
		$sFilename = (string) LIBRARY_PATH."/{$sClassName}.php";
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// Next we check the Framesie framework path
		$sFilename = (string) FRAMSIE_PATH."/{$sClassName}.php";
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// Next we check in the application path, so set the filename
		$sFilename = (string) APPLICATION_PATH."/{$sClassName}.php";
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// Next we check in the model path, so set the filename
		$sFilename = (string) MODEL_PATH."/{$sClassName}.php";
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// Finally we check in the controller path, so set the filename
		$sFilename = (string) CONTROLLER_PATH."/{$sClassName}.php";
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// If we have not returned by now, the class does not exist,
		// so we will throw a new exception
		throw new Exception("The class \"{$sClassName}\" could not be found.");
	}

	/**
	 * This method initializes and execute the framework (Frames)
	 * @package Bootstrap
	 * @subpackage Dispatcher
	 * @access public
	 * @return Bootstrap $this
	 */
	public function dispatch() {
		// Instantiate and execute Framsie
		Framsie::getInstance()->dispatch($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method turns error reporting and display on and off
	 * @package Bootstrap
	 * @access public
	 * @param boolean $bOnOff
	 * @return Bootstrap
	 */
	public function setErrorReporting($bOnOff) {
		// Do we need to turn errors on or off
		if ($bOnOff === true) { // We turn them on
			// Display errors
			ini_set('display_errors',  true);
			// Error reporting
			ini_set('error_reporting', E_ALL);
			// HTML errors
			ini_set('html_errors',     true);
		} else {                // We turn them off
			// Display errors
			ini_set('display_errors',  false);
			// HTML errors
			ini_set('html_errors',     false);
		}
		// Return the instance
		return $this;
	}
}
