<?php
/**
 * This class is processes the user request into a format that the framework
 * can understand and utilize
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieRequestObject {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property holds the singleton instance of the request object
	 * @access protected
	 * @staticvar FramsieRequestObject
	 */
	protected static $mInstance   = null;

	/**
	 * This property holds the name of the block file to be loaded
	 * for the view rendering
	 * @access protected
	 * @var sring
	 */
	protected $mBlock             = null;

	/**
	 * This property holds the name of the class to be loaded for the controller
	 * @access protected
	 * @var FramsieController
	 */
	protected $mController        = null;

	/**
	 * This property contains the cookies that came with the request
	 * @access protected
	 * @var stdClass
	 */
	protected $mCookies           = null;

	/**
	 * This property holds an objective instance of the $_GET variable
	 * @access protected
	 * @var stdClass
	 */
	protected $mGetRequest        = null;

	/**
	 * This property holds an objective instance of the $_POST variable
	 * @access protected
	 * @var stdClass
	 */
	protected $mPostRequest       = null;

	/**
	 * This property holds the SEO friendly GET request variables as well as the
	 * $_GET and $_POST global request variables
	 * @var stdClass
	 */
	protected $mQuery             = null;

	/**
	 * This property holds the parts of the request query that is currently
	 * being processed through the system
	 * @access protected
	 * @var array
	 */
	protected $mQueryInProcessing = array();

	/**
	 * This property holds the $_SERVER variable
	 * @access protected
	 * @var stdClass
	 */
	protected $mServer            = null;

	/**
	 * This property contains the sessions that came with the request
	 * @access protected
	 * @var stdClass
	 */
	protected $mSessions          = null;

	/**
	 * This property holds the request URI
	 * @access protected
	 * @var string
	 */
	protected $mRequest           = null;

	/**
	 * This property holds the static base uri
	 * @access protected
	 * @var string
	 */
	protected $mStaticBaseUri     = null;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method grants access to the single instance of the request object
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @static
	 * @param boolean [$bReset]
	 * @return FramsieRequestObject self::$mInstance
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
	 * This method sets an external instance into the class, this is primarily
	 * used in testing and generally with phpUnit
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @static
	 * @param FramsieRequestObject $oIsntance
	 * @return FramsieRequestObject self::$mInstance
	 */
	public static function setInstance(FramsieRequestObject $oInstance) {
		// Set the external instance into the class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply returns the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @return FramsieRequestObject $this
	 */
	public function __construct() {
		// Now we simply return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Processors ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method loads and validates the block view in the controller
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @throws FramsieException
	 * @return FramsieRequestObject $this
	 */
	protected function processBlock() {
		// Set the temporary block
		$sBlock = (string) ((empty($this->mQueryInProcessing[0]))) ? 'defaultView' : strtolower(preg_replace('/[^a-zA-Z]/', null, $this->mQueryInProcessing[0])).'View';
		// Check for the method
		if (!method_exists($this->mController, $sBlock)) {
			// Set the default block
			$sBlock = (string) 'defaultView';
			// Check for the view method in the controller
			if (!method_exists($this->mController, $sBlock)) {
				// Throw an exception because a block view is needed
				FramsieError::Trigger('FRAMVDE', array($sBlock, $this->mController));
			}
		} else {
			// Shift the block from the query in processing
			array_shift($this->mQueryInProcessing);
		}
		// Set the block into the system
		$this->mBlock = $sBlock;
		// Return the instance
		return $this;
	}

	/**
	 * This method grabs the controller from the processing request and sets it
	 * into this instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @throws FramsieException
	 * @return FramsieRequestObject $this
	 */
	protected function processController() {
		// Set the temporary controller
		$sController = (string) ((empty($this->mQueryInProcessing[0])) ? 'HomeController' : ucwords(strtolower(preg_replace('/[^a-zA-Z]/', null, $this->mQueryInProcessing[0]))).'Controller');
		// Check for the class
		if (!class_exists($sController)) {
			// Set the default controller
			$sController = (string) 'HomeController';
			// Make sure the default controller exists
			if (!class_exists($sController)) {
				// Throw an exception because the controller is invalid
				FramsieError::Trigger('FRAMCNE', array($sController));
			}
		} else {
			// Unset the controller from the request
			array_shift($this->mQueryInProcessing);
		}
		// Set the controller into the system
		$this->mController = (string) $sController;
		// Return the instance
		return $this;
	}

	/**
	 * This method processes the $_COOKIE variable into the system
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @return FramsieRequestObject $this
	 */
	protected function processCookies() {
		// Reset the cookie object
		$this->mCookies = new stdClass();
		// Loop through the cookies
		foreach ($_COOKIE as $sName => $sValue) {
			// Set the cookie into the system
			$this->mCookies->{$sName} = FramsieConverter::StringToPhpType($sValue);
		}
		// We're done, return the instance
		return $this;
	}

	/**
	 * This method process the $_GET variable into the system
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @return FramsieRequestObject $this
	 */
	protected function processGetRequest() {
		// Reset the get request object
		$this->mGetRequest = new stdClass();
		// Loop through the GET request
		foreach ($_GET as $sName => $sValue) {
			// Set the request variable into the system
			$this->mGetRequest->{$sName} = FramsieConverter::StringToPhpType($sValue);
		}
		// We're done, return the instance
		return $this;
	}

	/**
	 * This method processes the $_POST variable into the system
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @return FramsieRequestObject $this
	 */
	protected function processPostRequest() {
		// Reset the post request object
		$this->mPostRequest = new stdClass();
		// Loop through the POST request
		foreach ($_POST as $sName => $sValue) {
			// Set the request variable into the system
			$this->mPostRequest->{$sName} = FramsieConverter::StringToPhpType($sValue);
		}
		// We're done, return the instance
		return $this;
	}

	/**
	 * This method processes the remaining query in process into query parameters
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @return FramsieRequestObject $this
	 */
	protected function processQuery() {
		// Check for request variables
		if (!empty($this->mQueryInProcessing)) {
			// Create a query object placeholder
			$oQuery = new stdClass();
			// Loop through the parameters
			for ($iParameter = 0; $iParameter < count($this->mQueryInProcessing); ($iParameter += 2)) {
				// Make sure a valid key exists
				if ((empty($this->mQueryInProcessing[$iParameter]) === false) || (is_null($this->mQuery[$iParameter]) === false)) {
					// Set the parameter name
					$sName  = (string) urldecode($this->mQueryInProcessing[$iParameter]);
					// Set the parameter value
					if (empty($this->mQueryInProcessing[($iParameter + 1)]) && (is_null($this->mQueryInProcessing[($iParameter + 1)]) === true)) { // NULL
						// Set the value
						$sValue = null;
					} else {                                                                                                                       // True Type
						// Set the value
						$sValue = FramsieConverter::StringToPhpType(rawurldecode(rawurldecode($this->mQueryInProcessing[($iParameter + 1)])));
					}
					// Set the parameter
					$oQuery->{$sName} = $sValue;
				}
			}
			// Set the query into the system
			$this->mQuery = $oQuery;
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method processes the request URI into the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @return FramsieRequestObject $this
	 */
	protected function processRequest() {
		// Update the query in process
		$this->mQueryInProcessing = (string) $this->mRequest;
		// Check for a static base path
		if (empty($this->mStaticBaseUri) === false) {
			// Remove it from the request URI
			$this->mQueryInProcessing = (string) str_replace($this->mStaticBaseUri, null, $this->mQueryInProcessing);
		}
		// Separate the parts
		$this->mQueryInProcessing = (array) explode('/', $this->mQueryInProcessing);
		// Check for any empty array keys
		if (empty($this->mQueryInProcessing[0])) {
			// Shift this key off of the array
			array_shift($this->mQueryInProcessing);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method processes the $_SERVER variable into the system
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @return FramsieRequestObject $this
	 */
	protected function processServer() {
		// Reset the server object
		$this->mServer = new stdClass();
		// Loop through the SERVER object
		foreach ($_SERVER as $sProperty => $sValue) {
			// Formate the property name
			$sProperty = FramsieConverter::VariableNameToHungarianUpperCamelCase($sProperty);
			// Set the server variable into the system
			$this->mServer->{$sProperty} = FramsieConverter::StringToPhpType($sValue);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method processes the $_SESSION variable into the system
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access protected
	 * @return FramsieRequestObject $this
	 */
	protected function processSessions() {
		// Reset the session object
		$this->mSessions = new stdClass();
		// Loop through the sessions
		foreach ($_SESSION as $sName => $sValue) {
			// Set the session variable into the system
			$this->mSessions->{$sName} = FramsieConverter::StringToPhpType($sValue);
		}
		// We're done, return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method decodes and checks the true type of the variable and
	 * returns it as its true type
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sVariable
	 * @return boolean|number|string
	 */
	public function convertToTrueType($sVariable) {
		// First off we need to decode the variable
		$sVariable = urldecode($sVariable);
		// Check the variable type
		if (preg_match('/^false|true$/', $sVariable)) {                       // Boolean
			// Return the variable as a boolean
			return (boolean) (($sVariable === 'true') ? true : false);
		} elseif (preg_match('/^[0-9]+\.[0-9]+$/', $sVariable)) {                   // Floating Point
			// Return the variable as a floating point
			return (float) floatval($sVariable);
		} elseif (preg_match('/^[0-9]+$/', $sVariable)) {                        // Integer
			// Return the variable as an integer
			return (integer) intval($sVariable);
		} elseif (preg_match('/^null$/i', $sVariable) || empty($sVariable)) { // NULL
			// Return null
			return null;
		} else {
			// Return the variable as a string
			return (string) $sVariable;
		}
	}

	/**
	 * This method checks to see if a form has been submitted or an AJAX call
	 * has been placed to the application
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return boolean
	 */
	public function isPost() {
		// Check to see if this request is post
		if (empty($_POST) === false) {
			// POST variables exist, we have a POST request
			return true;
		}
		// Not a POST request
		return false;
	}

	/**
	 * This method processes the entire request object
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sRequest
	 * @param string $sStaticBasePath
	 * @return FramsieRequestObject $this
	 */
	public function process($sRequest, $sStaticBasePath = null) {
		// Remove the query string from the request
		$sRequest = (string) str_replace(array($_SERVER['QUERY_STRING'], '?'), null, $sRequest);
		// Set the request
		$this->mRequest       = (string) $sRequest;
		// Set the static base path
		$this->mStaticBaseUri = (string) $sStaticBasePath;
		// First we process the POST request (if any)
		$this->processPostRequest();
		// Next we proccess the GET request (if any)
		$this->processGetRequest();
		// Then we process the cookies
		$this->processCookies();
		// Then we process the SERVER
		$this->processServer();
		// Finally we process the sessions
		$this->processSessions();
		// Process the request string
		$this->processRequest();
		// Process the controller
		$this->processController();
		// Process the block view action
		$this->processBlock();
		// Process the query string and return
		$this->processQuery();
		// Execute the controller
		$this->mController = new $this->mController();
		// Set the request into the controller
		$this->mController->setRequest($this);
		// Set the view object into the controller
		$this->mController->setView(new FramsieView());
		// Set the block file in the controller
		// Set the block file into the controller view
		$this->mController->getView()->setBlockFile(strtolower(str_replace('Controller', null, get_class($this->mController))).'/'.strtolower(str_replace('View', null, $this->mBlock)).'.phtml');
		// Execute the view
		$this->mBlock      = $this->mController->{$this->mBlock}();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current block set in this instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return sring
	 */
	public function getBlock() {
		// Return the current block
		return $this->mBlock;
	}

	/**
	 * This method returns the current controller set in this instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return string
	 */
	public function getController() {
		// Return the current controller
		return $this->mController;
	}

	/**
	 * This method looks for a specific cookie in the system and returns
	 * its value if it exists, false otherwise
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sName
	 * @return multitype
	 */
	public function getCookie($sName) {
		// Check for the cookie
		if (empty($this->mCookies->{$sName}) || is_null($this->mCookies->{$sName})) {
			// Return false
			return null;
		}
		// Return the cookie variable
		return $this->mCookies->{$sName};
	}

	/**
	 * This method returns all of the cookies set in this instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return stdClass
	 */
	public function getCookies() {
		// Return the cookie object
		return $this->mCookies;
	}

	/**
	 * This method gets the current GET request object set in this instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return stdClass
	 */
	public function getGetRequest() {
		// Return the GET request
		return $this->mGetRequest;
	}

	/**
	 * This method looks for a parameter in the query objec, GET object and
	 * POST object, if none is found it returns false
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sName
	 * @return multitype
	 */
	public function getParam($sName) {
		// Check for the parameter in the query
		if ((empty($this->mQuery) === false) && property_exists($this->mQuery, $sName)) {
			// Return the query parameter
			return $this->mQuery->{$sName};
		}
		// Check for the parameter in the POST request
		if ((empty($this->mPostRequest) === false) && property_exists($this->mPostRequest, $sName)) {
			// Return the POST parameter
			return $this->mPostRequest->{$sName};
		}
		// Check for the parameter in the GET request
		if ((empty($this->mGetRequest) === false) && property_exists($this->mGetRequest, $sName)) {
			// Return the GET parameter
			return $this->mGetRequest->{$sName};
		}
		// Check for the parameter in the SERVER request
		if ((empty($this->mServer) === false) && property_exists($this->mServer, $sName)) {
			// Return the SERVER parameter
			return $this->mServer->{$sName};
		}
		// The parameter does not exist
		return null;
	}

	/**
	 * This method returns the current POST request object set in this instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return stdClass
	 */
	public function getPost() {
		// Return the POST request object
		return $this->mPostRequest;
	}

	/**
	 * This method returns the current query object set in this instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return stdClass
	 */
	public function getQueryObject() {
		// Return the query object
		return $this->mQuery;
	}

	/**
	 * This method returns the current REQUEST_URI set in this instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return string
	 */
	public function getRequest() {
		// Return the current request URI
		return $this->mRequest;
	}

	/**
	 * This method returns the current SERVER set in the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return stdClass
	 */
	public function getServer() {
		// Return the current SERVER request
		return $this->mServer;
	}

	/**
	 * This method looks for a session variable in the current session object,
	 * if none is found, false is returned
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sName
	 * @return multitype
	 */
	public function getSession($sName) {
		// Check for the session
		if (empty($this->mSessions->{$sName})) {
			// Return false
			return false;
		}
		// Return the session
		return $this->mSession->{$sName};
	}

	/**
	 * This method returns the current session object set in this instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return stdClass
	 */
	public function getSessions() {
		// Return the session object
		return $this->mSessions;
	}

	/**
	 * This method returns the current static base path which will be used to
	 * determine the path to where the framework should start processing the
	 * applications
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @return string
	 */
	public function getStaticUri() {
		// Return the static URI
		return $this->mStaticBaseUri;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets a custom block file into the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sBlock
	 * @return FramsieRequestObject $this
	 */
	public function setBlock($sBlock) {
		// Set the block into the instance
		$this->mBlock = (string) $sBlock;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets a custom controller into the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param FramsieController $sController
	 * @return FramsieRequestObject $this
	 */
	public function setController(FramsieController $oController) {
		// Set the controller into the instance
		$this->mController = $oController;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets a cookie into the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sName
	 * @param multitype $sValue
	 * @param integer [$iExpire]
	 * @return FramsieRequestObject $this
	 */
	public function setCookie($sName, $sValue, $iExpire = 3600) {
		// Set the cookie
		setcookie($sName, $sValue, (time() + $iExpire));
		// Set the cookie into the instance
		$this->mCookies->{$sName} = $sValue;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets a parameter into the request object in the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sName
	 * @param multitype $sValue
	 * @return FramsieRequestObject $this
	 */
	public function setParam($sName, $sValue) {
		// Set the parameter into the query
		$this->mQuery->{$sName} = $sValue;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the REQUEST_URI into the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sRequestUri
	 * @return FramsieRequestObject $this
	 */
	public function setRequest($sRequestUri) {
		// Set the REQUEST_URI into the instance
		$this->mRequest = (string) preg_replace('/\?.*/', null, $sRequestUri);
		// Return the instance
		return $this;
	}

	/**
	 * This method sets a session variable into the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @access public
	 * @param string $sName
	 * @param multitype $sValue
	 * @return FramsieRequestObject $this
	 */
	public function setSession($sName, $sValue) {
		// Set the session var into the global
		$_SESSION[$sName] = $sValue;
		// Set the session var into the instance
		$this->mSessions->{$sName} = $sValue;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the static URI into the instance
	 * @package Framsie
	 * @subpackage FramsieRequestObject
	 * @param string $sUri
	 * @return FramsieRequestObject $this
	 */
	public function setStaticUri($sUri) {
		// Set the static URI into the instance
		$this->mStaticBaseUri = (string) $sUri;
		// Return the instance
		return $this;
	}
}
