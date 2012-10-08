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
		// Define the static file path
		if (defined('STATIC_FILE_PATH') === false) {
			define('STATIC_FILE_PATH',        dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public');
		}
		// Define the library path
		if (defined('LIBRARY_PATH') === false) {
			define('LIBRARY_PATH',            dirname(__FILE__));
		}
		// Define the Frames path
		if (defined('FRAMSIE_PATH') === false) {
			define('FRAMSIE_PATH',            LIBRARY_PATH.DIRECTORY_SEPARATOR.'framsie');
		}
		// Define the application path
		if (defined('APPLICATION_PATH') === false) {
			define('APPLICATION_PATH',        LIBRARY_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'application');
		}
		// Define the model path
		if (defined('MODEL_PATH') === false) {
			define('MODEL_PATH',              APPLICATION_PATH.DIRECTORY_SEPARATOR.'models');
		}
		// Define the controller path
		if (defined('CONTROLLER_PATH') === false) {
			define('CONTROLLER_PATH',         APPLICATION_PATH.DIRECTORY_SEPARATOR.'controllers');
		}
		// Define the block path
		if (defined('BLOCK_PATH') === false) {
			define('BLOCK_PATH',              APPLICATION_PATH.DIRECTORY_SEPARATOR.'blocks');
		}
		// Define the JS assets path
		if (defined('JAVASCRIPT_ASSETS_PATH') === false) {
			define('JAVASCRIPT_ASSETS_PATH',  'assets'.DIRECTORY_SEPARATOR.'js');
		}
		// Define the CSS assets path
		if (defined('CSS_ASSETS_PATH') === false) {
			define('CSS_ASSETS_PATH',         'assets'.DIRECTORY_SEPARATOR.'css');
		}
		// Define the Image assets path
		if (defined('IMG_ASSETS_PATH') === false) {
			define('IMG_ASSETS_PATH',         'assets'.DIRECTORY_SEPARATOR.'img');
		}
		// Define the include path
		if (defined('INCLUDE_PATH') === false) {
			define('INCLUDE_PATH',            dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'includes');
		}
		// Define the cache path
		if (defined('CACHE_DIRECTORY') === false) {
			define('CACHE_DIRECTORY',         INCLUDE_PATH.DIRECTORY_SEPARATOR.'cache');
		}
		// Define the configuration path
		if (defined('CONFIGURATION_FILE_PATH') === false) {
			define('CONFIGURATION_FILE_PATH', APPLICATION_PATH.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'application.ini');
		}
		// Define the flat file database path
		if (defined('FLAT_FILE_DB_PATH') === false) {
			define('FLAT_FILE_DB_PATH',       APPLICATION_PATH.DIRECTORY_SEPARATOR.'db');
		}
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
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method loads redirect URLs into the framework
	 * @package Framsie
	 * @subpackage Bootstrap
	 * @access protected
	 * @return Bootstrap $this
	 */
	protected function loadRedirects() {
		// Redirect faq to faqs
		Framsie::getInstance()->addRedirect('/home/faq', '/home/faqs');
		// Return the instance
		return $this;
	}

	/**
	 * Put all of your Framsie calls here that aren't default so the system is
	 * fully initialized to your application upon execution
	 * @package Bootstrap
	 * @access protected
	 * @return Bootstrap $this
	 */
	protected function onBeforeDispatch() {
		// Initialize FramsieError with the database
		FramsieError::InitializeErrorsFromDatabase('ErrorLookup', 'Code', 'Message');
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method generates a singleton of any existing class passed to it
	 * @package Bootstrap
	 * @access public
	 * @static
	 * @param string $sClassName
	 * @param boolean [$bReset]
	 * @throws FramsieException
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
		FramsieError::Trigger('FRAMINF', array($sClassName));
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method automagically loads class files (if they exist)
	 * @package Bootstrap
	 * @access public
	 * @param string $sClassName
	 * @throws FramsieException
	 * @return void
	 */
	public function autoLoader($sClassName) {
		// Create an array of reserved class names
		$aReservedClassNames = array(
			'Form',
			'FormElement',
			'Framsie',
			'HttpResponseMapper',
			'Mapper',
			'TableMapper'
		);
		// Check for a Framsie package class
		if ((strpos($sClassName, 'Framsie') !== false) && (in_array($sClassName, $aReservedClassNames) === false)) {
			// Replace the the class name
			$sClassName = (string) str_replace('Framsie', null, $sClassName);
		}

		// Check for a mapper
		if ((strpos($sClassName, 'Mapper') !== false) && (in_array($sClassName, $aReservedClassNames) === false)) {
			// Replace the class name
			$sClassName = (string) str_replace('Mapper', null, $sClassName);
			// Set the directory
			$sClassName = (string) 'mappers'.DIRECTORY_SEPARATOR.$sClassName;
		}

		// Check for a form
		if ((strpos($sClassName, 'Form') !== false) && (in_array($sClassName, $aReservedClassNames) === false)) {
			// Replace the class name
			$sClassName = (string) str_replace('Form', null, $sClassName);
			// Set the directory
			$sClassName = (string) 'forms'.DIRECTORY_SEPARATOR.$sClassName;
		}

		// First we check in the library path, so set the filename
		$sFilename = (string) LIBRARY_PATH.DIRECTORY_SEPARATOR.$sClassName.'.php';
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// Next we check the Framesie framework path
		$sFilename = (string) FRAMSIE_PATH.DIRECTORY_SEPARATOR.$sClassName.'.php';
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// Next we check in the application path, so set the filename
		$sFilename = (string) APPLICATION_PATH.DIRECTORY_SEPARATOR.$sClassName.'.php';
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// Next we check in the model path, so set the filename
		$sFilename = (string) MODEL_PATH.DIRECTORY_SEPARATOR.$sClassName.'.php';
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// Finally we check in the controller path, so set the filename
		$sFilename = (string) CONTROLLER_PATH.DIRECTORY_SEPARATOR.$sClassName.'.php';
		// Check for the file
		if (file_exists($sFilename)) {
			// Load the file
			require_once($sFilename);
			// We're done
			return;
		}

		// If we have not returned by now, the class does not exist,
		// so we will throw a new exception
		FramsieError::Trigger('FRAMCNF', array($sClassName));
	}

	/**
	 * This method initializes and execute the framework (Frames)
	 * @package Bootstrap
	 * @subpackage Dispatcher
	 * @access public
	 * @return Bootstrap $this
	 */
	public function dispatch() {
		// Check to see if an application environment has been set
		if (defined('APPLICATION_ENVIRONMENT') === false) {
			// Define it
			$this->setEnvironment(Framsie::ENV_PRODUCTION);
		}
		// Load the redirects
		$this->loadRedirects();
		// Initialize the framework
		$this->onBeforeDispatch();
		// Instantiate and execute Framsie
		Framsie::getInstance()->dispatch($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the application environment into the system
	 * @package Bootstrap
	 * @access public
	 * @param string $sEnvironment
	 * @return Bootstrap
	 */
	public function setEnvironment($sEnvironment) {
		// Check to see if an application environment has been defined
		if (defined('APPLICATION_ENVIROMENT') === false) {
			// Set the applicaiton environment
			define('APPLICATION_ENVIRONMENT', $sEnvironment);
		}
		// Check the environment
		if (APPLICATION_ENVIRONMENT == (Framsie::ENV_DEVELOPMENT || Framsie::ENV_STAGING)) {
			// Turn errors on
			$this->setErrorReporting(true);
		} else {
			// Turn errors off
			$this->setErrorReporting(false);
		}
		// Return the instance
		return $this;
	}

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
