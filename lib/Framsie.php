<?php
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
	 * This property contains a globally accesssible instance of the request object
	 * @access protected
	 * @var FramsieRequestObject
	 */
	protected $mRequest          = null;

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
	 * @return object self::$mInstances[$sClass]
	 */
	public static function Singleton() {
		// Grab the arguments
		$aArguments = func_get_args();
		// Set the class name
		$sClass = (string) $aArguments[0];
		// Remove the class name
		array_shift($aArguments);
		// Check for an existing instance
		if (empty(self::$mInstances[$sClass]) || ($bReset === true)) {
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
	 * This method is simply a helper to the objects of Framsie to dynamically
	 * load the singleton patterns of controllers and models
	 * @package Framsie
	 * @access public
	 * @static
	 * @param string $sClassName
	 * @throws Exception
	 * @return multitype
	 */
	public static function Instance($sClassName) {
		// Make sure the class exists
		if (class_exists($sClassName)) {
			// Return an instance
			return $sClassName::getInstance();
		}
		// Throw a new exception
		throw new Exception("The class \"{$sClassName}\" does not exist.");
	}

	/**
	 * This method creates a new instance of a class
	 * @package Framsie
	 * @access public
	 * @static
	 * @param string $sClassName
	 * @throws Exception
	 * @return multitype
	 */
	public static function Instantiate($sClassName) {
		// Make sure the class exists
		if (class_exists($sClassName)) {
			// Return a new instance
			return new $sClassName();
		}
		// Throw a new exception
		throw new Exception('The class \"{$sClassName}\" does not exist.');
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
	 * This method sets up the request and processes it returning the block to the user
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sHostName
	 * @param string $sRequest
	 * @param string [$sStaticBase]
	 * @return Framsie $this
	 */
	public function dispatch($sHostName, $sRequest, $sStaticBase = null) {
		// Check for an invalid request
		if ($this->isInvalidRequest($sRequest)) {
			// Throw a new exception
			throw new Exception('The request URL is invalid.');
		}
		// Process the request
		FramsieRequestObject::getInstance()->process($this->matchRedirects($sRequest), $sStaticBase);
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
	 * @throws Exception
	 * @return string
	 */
	public function renderBlock($sFilename) {
		// Check for an extension
		if (!preg_match('/\.css|js|php|phtml$/i', $sFilename)) {
			// Append the file extension to the filename
			$sFilename .= (string) "{$sFilename}.phtml";
		}
		// Make sure the file exists
		if (!file_exists(BLOCK_PATH."/{$sFilename}")) {
			// Throw an exception because if this method is called, obviously
			// the block is needed to continue
			throw new Exception("The block file \"{$sFilename}\" does not exist as it was called, nor does it exist in the blocks directory");
		}
		// Start the capture of the output buffer stream
		ob_start();
		// Load the block
		require_once(BLOCK_PATH."/{$sFilename}");
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
}