<?php
/**
 * This class provides the structure for routes
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieRouter {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of this class
	 * @access protected
	 * @static
	 * @var FramsieRouter self
	 */
	protected static $mInstance = null;

	/**
	 * This property contains the default route data
	 * @access protected
	 * @var stdClass
	 */
	protected $mDefaultRoute    = null;

	/**
	 * This property contains an array of route objects
	 * @access protected
	 * @var Array<stdClass>
	 */
	protected $mRoutes          = array();

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method maintains the singleton instance of this class
	 * @package Framsie
	 * @subpackage FramsieRouter
	 * @access public
	 * @static
	 * @param boolean $bReset [false]
	 * @return FramsieRouter self::$mInstance
	 */
	public static function getInstance($bReset = false) {
		// Check for an existing instance
		if (empty(self::$mInstance)) {
			// Create a new instance
			self::$mInstance = new self();
		}
		// Return the instance
		return self::$mInstance;
	}

	/**
	 * This method sets an external instance into the class
	 * @package Framsie
	 * @subpackage FramsieRouter
	 * @access public
	 * @param FramsieRouter $oInstance
	 * @return FramsieRouter self::$mInstance
	 */
	public static function setInstance(FramsieRouter $oInstance) {
		// Set the external instance into this class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constructor sets up the instance with an initial default route
	 * @package Framsie
	 * @subpackage FramsieRouter
	 * @access public
	 * @return FramsieRouter $this
	 */
	public function __construct() {
		// Initialize the default route object
		$this->mDefaultRoute = new StdClass();
		// Set the default route request URI
		$this->mDefaultRoute->mRequestUri = (string) '/';
		// Set the default route controller
		$this->mDefaultRoute->mController = (string) 'Home';
		// Set the default route action
		$this->mDefaultRoute->mAction     = (string) 'Default';
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds a route into the system
	 * @package Framsie
	 * @subpackage FramsieRouter
	 * @access public
	 * @param string $sName
	 * @param string $sUri
	 * @param string $sController
	 * @param string $sAction
	 * @return FramsieRouter $this
	 */
	public function addRoute($sName, $sUri, $sController, $sAction) {
		// Setup the route object
		$oRoute                = new StdClass();
		// Set the URI request
		$oRoute->mRequestUri   = (string) $sUri;
		// Set the controller to load
		$oRoute->mController   = (string) $sController;
		// Set the action
		$oRoute->mAction       = (string) $sAction;
		// Append the route to the system
		$this->mRoutes[$sName] = $oRoute;
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current default route from the instance
	 * @package Framsie
	 * @subpackage FramsieRouter
	 * @access public
	 * @return StdClass $this->mDefaultRoute
	 */
	public function getDefaultRoute() {
		// Return the default route from the instance
		return $this->mDefaultRoute;
	}

	/**
	 * This method returns a route from the instance
	 * @package Framsie
	 * @subpackage FramsieRouter
	 * @access public
	 * @throws Exception
	 * @param string $sName
	 * @return StdClass $this->mRoutes[$sName]
	 */
	public function getRoute($sName) {
		// Check for the route
		if (empty($this->mRoutes[$sName])) {
			// Throw an exception
			throw new Exception("The route \"{$sName}\" doesn not exist in the current stack.");
		}
		// Return the route
		return $this->mRoutes[$sName];
	}

	/**
	 * This method returns the current array of routes from the instance
	 * @package Framsie
	 * @subpackage FramsieRouter
	 * @access public
	 * @return Array<StdClass>
	 */
	public function getRoutes() {
		// Return the current routes from the instance
		return $this->mRoutes;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the default route into the instance
	 * @package Framsie
	 * @subpackage FramsieRouter
	 * @access public
	 * @param string $sUri
	 * @param string $sController
	 * @param string $sAction
	 * @return FramsieRouter $this
	 */
	public function setDefaultRoute($sUri, $sController, $sAction) {
		// Initialize the default route
		$this->mDefaultRoute              = new StdClass();
		// Set the default route request URI
		$this->mDefaultRoute->mRequestUri = (string) $sUri;
		// Set the default route controller
		$this->mDefaultRoute->mController = (string) $sController;
		// Set the default route action
		$this->mDefaultRoute->mAction     = (string) $sAction;
		// Return the instance
		return $this;
	}
}
