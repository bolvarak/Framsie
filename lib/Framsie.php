<?php session_start(); // Initialize Sessions
/**
 * This class is the backbone of Frames, it sets up the request object
 * and processes said request object to run the application in a MVC pattern
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class Framsie {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant defines the development environment name
	 * @var string
	 */
	const ENV_DEVELOPMENT  = 'devel';

	/**
	 * This constant defines the production environment name
	 * @var string
	 */
	const ENV_PRODUCTION   = 'prod';

	/**
	 * This constant defines the staging environment name
	 * @var string
	 */
	const ENV_STAGING      = 'stg';

	/**
	 * This constant contains the Framsie notation for template strings
	 * @var string
	 */
	const FRAMSIE_NOTATION = ':=';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of Frames
	 * @access protected
	 * @staticvar Framsie
	 */
	protected static $mInstance  = null;

	/**
	 * This property contains an array of class instances
	 * @access protected
	 * @staticvar array
	 */
	protected static $mInstances = array();

	/**
	 * This property contains custom class paths
	 * @access protected
	 * @var array
	 */
	protected $mCustomClassPaths = array();

	/**
	 * This property contains a globally accesssible instance of the request object
	 * @access protected
	 * @var FramsieRequestObject
	 */
	protected $mRequest          = null;

	/**
	 * This property contains the pre-dispatch required files
	 * @access protected
	 * @var array
	 */
	protected $mRequiredFiles    = array();

	/**
	 * This property contains the redirects URLs for custom URL structures
	 * @access protected
	 * @var array
	 */
	protected $mRedirects        = array();

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets and maintains a single instance of Framsie at all times
	 * @package Framsie
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return Framsie self::$mInstance
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
	 * This method sets an external instance of Framsie into the class,
	 * this is generally only used in testing, primarily with phpUnit
	 * @package Framsie
	 * @access public
	 * @static
	 * @param Framsie $oInstance
	 * @return Framsie self::$mInstance
	 */
	public static function setInstance(Framsie $oInstance) {
		// Set the current instance to the external instance
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	/**
	 * This method creates a singleton out of a normal class
	 * @package Framsie
	 * @access public
	 * @static
	 * @param string $sClass
	 * @param Dynamic constructor arguments
	 * @return instance self::$mInstances[$sClass]
	 */
	public static function Singleton() {
		// Grab the arguments
		$aArguments = func_get_args();
		// Set the class name
		$sClass = (string) $aArguments[0];
		// Remove the class name
		array_shift($aArguments);
		// Check for an existing instance
		if (empty(self::$mInstances[$sClass])) {
			// Check for arguments to pass
			if (empty($aArguments)) {
				// Create a new instance
				self::$mInstances[$sClass] = new $sClass();
			} else {
				// Create an instance of the reflection class
				$oReflection = new ReflectionClass($sClass);
				// Create a new instance
				self::$mInstances[$sClass] = $oReflection->newInstanceArgs($aArguments);
			}
		}
		// Return the instance
		return self::$mInstances[$sClass];
	}

	/**
	 * This method resets a singleton instance set into the class
	 * @package Framsie
	 * @access public
	 * @static
	 * @param string $sClass
	 * @return instance self::$mInstances[$sClass]
	 */
	public static function SingletonReset() {
		// Grab the arguments
		$aArguments = func_get_args();
		// Set the class name
		$sClass = (string) $aArguments[0];
		// Remove the class name
		array_shift($aArguments);
		// Check for arguments to pass
		if (empty($aArguments)) {
			// Create a new instance
			self::$mInstances[$sClass] = new $sClass();
		} else {
			// Create an instance of the reflection class
			$oReflection = new ReflectionClass($sClass);
			// Create a new instance
			self::$mInstances[$sClass] = $oReflection->newInstanceArgs($aArguments);
		}
		// Return the instance
		return self::$mInstances[$sClass];
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This is our constructor, it simply returns an instance and is protected
	 * to ensure the use of the singleton pattern
	 * @package Framsie
	 * @access protected
	 * @return Framsie $this
	 */
	protected function __construct() {
		// Simply return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets up the defined class paths for Framsie
	 * @package Framsie
	 * @access public
	 * @static
	 * @return Framsie self::$mInstance;
	 */
	public static function Bootstrap() {
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
		spl_autoload_register(array(self::getInstance(), 'autoLoader'));
		// Setup the error handler
		// set_error_handler    (array($this, 'dispatchErrors'));
		// Setup the exception handler
		set_exception_handler(array(self::getInstance(), 'dispatchException'));
		// Return the instance of Framsie
		return self::getInstance();
	}

	/**
	 * This method runs a check on an array to determine if it is associative or numerically indexed
	 * @package Framsie
	 * @access public
	 * @static
	 * @param array $aEntity
	 * @return boolean
	 */
	public static function IsAssociatiative(array $aEntity) {
		// Check the array
		return (boolean) count(array_filter(array_keys($aEntity), 'is_string'));
	}

	/**
	 * This method does a check to see if the variable is actually empty
	 * @package Framsie
	 * @access public
	 * @static
	 * @param mixed $mEntity
	 * @return boolean
	 */
	public static function IsEmpty($mEntity) {
		// Check to see if the entity variable is empty or not
		if (empty($mEntity) && is_null($mEntity)) {
			// The entity is most definitely empty
			return true;
		}
		// The entity is not empty
		return false;
	}

	/**
	 * This method dynamically loads new instances of classes into the workflow
	 * @package Framsie
	 * @access public
	 * @static
	 * @param string $sClass
	 * @param string $sArgument
	 * @return object
	 */
	public static function Loader() {
		// Grab the arguments
		$aArguments = func_get_args();
		// Set the class name
		$sClass     = (string) $aArguments[0];
		// Create an instance placeholder
		$oInstance  = null;
		// Remove the class name
		array_shift($aArguments);
		// Check for an existing instance
		if (empty(self::$mInstances[$sClass])) {
			// Check for arguments to pass
			if (empty($aArguments)) {
				// Create a new instance
				$oInstance = new $sClass();
			} else {
				// Create an instance of the reflection class
				$oReflection = new ReflectionClass($sClass);
				// Create a new instance
				$oInstance = $oReflection->newInstanceArgs($aArguments);
			}
		}
		// Return the instance
		return $oInstance;
	}

	/**
	 * This method provides a string template variable replacement system for preparing template strings
	 * @package Framsie
	 * @access public
	 * @static
	 * @param string $sString
	 * @param array $aReplacements
	 * @param string $sNotation
	 * @throws FramsieException
	 * @return string
	 */
	public static function PrepareString($sString, $aReplacements = array(), $sNotation = self::FRAMSIE_NOTATION) {
		// Grab the number of occurrences of the notator
		$iOccurrences = substr_count($sString, $sNotation);
		// Check the number of replacements for too many
		if (count($aReplacements) < $iOccurrences) {
			// Trigger an exception
			self::Trigger('FRAMTMR');
		}
		// Check the number of replacements for too few
		if (count($aReplacements) > $iOccurrences) {
			// Trigger an exception
			self::Trigger('FRAMTFR');
		}
		// Loop through the occurrences
		for ($iOccurrence = 0; $iOccurrence < $iOccurrences; $iOccurrence++) {
			// Make the replacement
			$sString = (string) substr_replace($sString, $aReplacements[0], strpos($sString, $sNotation), strlen($sNotation));
			// Remove the this replacement
			array_shift($aReplacements);
		}
		// Return the proper error message
		return $sString;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method handles the displaying of the layout and view currently set in the system
	 * @package Framsie
	 * @access protected
	 * @return Framsie $this
	 */
	protected function dispatchLayout() {
		// Check for a layout
		if ($this->getController()->getDisableLayout() === true) {
			// Simply render the view
			echo $this->getController()->getView()->renderView();
		} else {
			// Render the layout
			echo $this->getController()->renderLayout();
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method checks the request against a list of known invalid requests
	 * @package Framsie
	 * @access protected
	 * @param string $sRequest
	 * @return boolean
	 */
	protected function isInvalidRequest($sRequest) {
		// Setup the invalids array
		$aInvalids = array(
			'favicon.ico'
		);
		// Loop through the invalids
		foreach ($aInvalids as $sInvalidRequest) {
			// Check the request
			if (strpos($sRequest, $sInvalidRequest) !== false) {
				// An invalid request exists, return
				return true;
			}
		}
		// All requests are valid, return
		return false;
	}

	/**
	 * This method runs all of the processes that must be executed prior to dispatch
	 * @package Framsie
	 * @access protected
	 * @return Framsie
	 */
	protected function onBeforeDispatch() {
		// Check to see if an application environment has been set
		if (defined('APPLICATION_ENVIRONMENT') === false) {
			// Define it
			$this->setEnvironment(Framsie::ENV_PRODUCTION);
		}
		// Loop through the pre-dispatch files and load them
		foreach ($this->mRequiredFiles as $sFile) {
			// Load the file
			require_once($sFile);
		}
		// Return the isntance
		return $this;
	}

	/**
	 * This method checks the current request against the pre-defined redirects
	 * @package Framsie
	 * @access protected
	 * @param string $sRequest
	 * @return string
	 */
	protected function matchRedirects($sRequest) {
		// Loop through the redirects
		foreach ($this->mRedirects as $sSource => $sTarget) {
			// Check the source
			if (strpos($sRequest, $sSource) !== false) {
				// Reset the request
				$sRequest = (string) $sTarget;
			}
		}
		// Return the request
		return $sRequest;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds a custom class path to the system
	 * @package Framsie
	 * @access public
	 * @param string $sPath
	 * @throws FramsieException
	 * @return Framsie $this
	 */
	public function addCustomClassPath($sPath) {
		// Check the last character of the string
		if (substr($sPath, -1) === DIRECTORY_SEPARATOR) {
			// Remove the directory separator
			$sPath = rtrim($sPath, DIRECTORY_SEPARATOR);
		}
		// Make sure the file exists
		if (file_exists($sPath) === false) {
			// Trigger an exception
			FramsieError::Trigger('FRAMFMI', array($sPath));
		}
		// Add the path
		array_push($this->mCustomClassPaths, $sPath);
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a redirect rule to the instance
	 * @package Framsie
	 * @access public
	 * @param string $sSource
	 * @param string $sTarget
	 * @return Framsie $this
	 */
	public function addRedirect($sSource, $sTarget) {
		// Add the redirect to the system
		$this->mRedirects[$sSource] = (string) $sTarget;
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a file to the system that must be loaded before Framsie is dispatched
	 * @package Framsie
	 * @param string $sFile
	 * @throws FramsieException
	 * @return Framsie $this
	 */
	public function addRequiredFile($sFile) {
		// Make sure the file exists
		if (file_exists($sFile) === false) {
			// Trigger an exception
			FramsieError::Trigger('FRAMFMI', array($sFile));
		}
		// Add the file to the array
		array_push($this->mRequiredFiles, $sFile);
		// Return the instance
		return $this;
	}

	/**
	 * This method automagically loads class files (if they exist)
	 * @package Framsie
	 * @access public
	 * @param string $sClassName
	 * @throws FramsieException
	 * @return void
	 */
	public function autoLoader($sClassName) {
		// Create an array of reserved class names
		$aReservedClassNames = array(
				'DatabaseMapper',
				'Form',
				'FormElement',
				'Framsie',
				'HttpResponseMapper',
				'Mapper',
				'String',
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

		// Loop through the custom class paths as well
		foreach ($this->mCustomClassPaths as $sPath) {
			// Check to see if the file exists
			if (file_exists($sPath.DIRECTORY_SEPARATOR.$sFilename)) {
				// Load the file
				require_once($sPath.DIRECTORY_SEPARATOR.$sFilename);
				// We're done
				return;
			}
		}

		// If we have not returned by now, the class does not exist,
		// so we will throw a new exception
		FramsieError::Trigger('FRAMCNF', array($sClassName));
	}

	/**
	 * This method sets up the request and processes it returning the block to the user
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sHostName
	 * @param string $sRequest
	 * @param string [$sStaticBase]
	 * @throws FramsieException
	 * @return Framsie $this
	 */
	public function dispatch($sStaticBase = null) {
		// Check for an invalid request
		if ($this->isInvalidRequest($_SERVER['REQUEST_URI'])) {
			// Throw a new exception
			FramsieError::Trigger('FRAMIRQ', array($_SERVER['REQUEST_URI']));
		}
		// Execute the pre-dispatch hooks
		$this->onBeforeDispatch();
		// Process the request
		FramsieRequestObject::getInstance()->process($this->matchRedirects($_SERVER['REQUEST_URI']), $sStaticBase);
		// Process the layout
		$this->dispatchLayout();
		// Return the instance
		return $this;
	}

	/**
	 * This method handles exceptions in the system
	 * @package Framsie
	 * @access public
	 * @param Exception $oException
	 * @return Framsie $this
	 */
	public function dispatchException($oException) {
		// Process the request
		FramsieRequestObject::getInstance()->process('/error/default', null);
		// Grab the controller and set the exception into the view
		$this->getController()->getView()->oException = $oException;
		// Process the layout
		$this->dispatchLayout();
		// Return the instance
		return $this;
	}

	/**
	 * This method renders a block file that is included in the application
	 * @package Framsie
	 * @access public
	 * @param string $sFilename
	 * @throws FramsieException
	 * @return string
	 */
	public function renderBlock($sFilename) {
		// Check for an extension
		if (!preg_match('/\.css|js|php|phtml$/i', $sFilename)) {
			// Append the file extension to the filename
			$sFilename .= (string) "{$sFilename}.phtml";
		}
		// Make sure the file exists
		if (!file_exists(BLOCK_PATH.DIRECTORY_SEPARATOR.$sFilename)) {
			// Throw an exception because if this method is called, obviously
			// the block is needed to continue
			FramsieError::Trigger('FRAMBNE', array($sFilename));
		}
		// Start the capture of the output buffer stream
		ob_start();
		// Load the block
		require_once(BLOCK_PATH.DIRECTORY_SEPARATOR.$sFilename);
		// Depending on the print notification either return the buffer
		// or simply print the buffer directly to the screen
		return ob_get_clean();
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current controller instance stored in the instance
	 * @package Framsie
	 * @access public
	 * @return FramsieController
	 */
	public function getController() {
		// Return the current controller from the request object
		return FramsieRequestObject::getInstance()->getController();
	}

	/**
	 * This method returns the current request object in the instance
	 * @package Framsie
	 * @access public
	 * @return FramsieRequestObject
	 */
	public function getRequest() {
		// Return the current request in the system
		return FramsieRequestObject::getInstance();
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the application environment into the system
	 * @package Framsie
	 * @access public
	 * @param string $sEnvironment
	 * @return Framsie
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
	 * @package Framsie
	 * @access public
	 * @param boolean $bOnOff
	 * @return Framsie
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
