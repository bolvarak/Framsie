<?php
/**
 * This class provides easy Web requests
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
abstract class FramsieHttp {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the HTML type definition
	 * @var integer
	 */
	const DATA_TYPE_HTML      = 1;

	/**
	 * This constant contains the JSON type definition
	 * @var integer
	 */
	const DATA_TYPE_JSON      = 2;

	/**
	 * This constant contains the QUERY_STRING type definition
	 * @var integer
	 */
	const DATA_TYPE_QUERY_STR = 3;

	/**
	 * This constant contains the SCRIPT type definition
	 * @var integer
	 */
	const DATA_TYPE_SCRIPT    = 4;

	/**
	 * This constant contains the TEXT type definition
	 * @var integer
	 */
	const DATA_TYPE_TEXT      = 5;

	/**
	 * This constant contains the XML type definition
	 * @var integer
	 */
	const DATA_TYPE_XML       = 6;

	/**
	 * This constant contains the GET type definition
	 * @var integer
	 */
	const REQUEST_METHOD_GET  = 1;

	/**
	 * This constant contains the POST type definition
	 * @var integer
	 */
	const REQUEST_METHOD_POST = 2;

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of this class
	 * @access protected
	 * @staticvar FramsieHttp
	 */
	protected static $mInstance   = null;

	/**
	 * This property contains the data for the request
	 * @access protected
	 * @var array
	 */
	protected $mData          = array();

	/**
	 * This property contains the data type
	 * @access protected
	 * @var string
	 */
	protected $mDataType      = null;

	/**
	 * This property contains the request headers
	 * @access protected
	 * @var array
	 */
	protected $mHeaders       = array();

	/**
	 * This property contains the last sent request
	 * @access protected
	 * @var stdClass
	 */
	protected $mLastRequest   = null;

	/**
	 * This property contains the last server response
	 * @access protected
	 * @var stdClass
	 */
	protected $mLastResponse  = null;

	/**
	 * This property contains the HTTP password
	 * @access protected
	 * @var string
	 */
	protected $mPassword      = null;

	/**
	 * This property contains hooks that should be executed before the request is made
	 * @access protected
	 * @var arrau
	 */
	protected $mRequestHooks  = array();

	/**
	 * This property contains the request method
	 * @access protected
	 * @var string
	 */
	protected $mRequestMethod = null;

	/**
	 * This property contains the decoded response from the server
	 * @access protected
	 * @var array
	 */
	protected $mResponse      = array();

	/**
	 * This property contains the hooks that should be executed upon response
	 * @access protected
	 * @var array
	 */
	protected $mResponseHooks = array();

	/**
	 * This property contains the request url
	 * @access protected
	 * @var string
	 */
	protected $mUrl           = null;

	/**
	 * This property contains the HTTP username
	 * @access protected
	 * @var string
	 */
	protected $mUsername      = null;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method provides access to the singleton instance of this class
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieHttp self::$mInstance
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
	 * This method sets an external instance into this class, it is primarily
	 * only used in testing and generally with phpUnit
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @static
	 * @param FramsieHttp $oInstance
	 * @return FramsieHttp self::$mInstance
	 */
	public static function setInstance(FramsieHttp $oInstance) {
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
	 * @subpackage FramsieHttp
	 * @access public
	 * @return FramsieHttp $this
	 */
	public function __construct() {
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method encodes the parameters in the data array into a query string
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access protected
	 * @param array $aParameters
	 * @return string
	 */
	protected function encodeDataParams($aParameters = array()) {
		// Check to see if we need to encode a custom data set
		if (empty($aParameters) === false) {
			// Encode the custom data array
			return http_build_query($aParameters);
		}
		// Return the Query String version of the parameters in the system
		return http_build_query($this->mData);
	}

	/**
	 * This method ensures that the hook methods being added to the class actually exist
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access protected
	 * @param multitype $mMethod
	 * @throws Exception
	 * @return FramsieHttp $this
	 */
	protected function ensureHookMethod($mMethod) {
		// Check to see if an object was send with the method
		if (is_array($mMethod)) {
			// Check to make sure there is an actual object
			if (is_object($mMethod[0]) === false) {
				// Throw an exception
				throw new Exception("No valid instance was provided with the hook method");
			}
			// Check to see if the method exists
			if (method_exists($mMethod[0], $mMethod[1]) === false) {
				// Throw an exception
				throw new Exception("The hook method \"{$mMethod[1]}\" does not exist in the class \"".get_class($mMethod[0])."\".");
			}
			// We're done, return the instance
			return $this;
		}
		// The method is associated with this object, make sure it exists
		if (method_exists($this, $mMethod) === false) {
			// Throw an exception
			throw new Exception("The hook method \"{$mMethod}\" does not exist in the class \"".get_class($this)."\".");
		}
		// We're done, return the instance
		return $this;
	}

	/**
	 * This method loops through the request hooks and executes them
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access protected
	 * @return FramsieHttp $this
	 */
	protected function executeRequestHooks() {
		// Loop through the request hooks and execute them
		foreach ($this->mRequestHooks as $sMethod) {
			// Check to see if the method has an object associated with it
			if (is_array($sMethod) === true) {
				// Execute the hook
				call_user_func($sMethod);
			} else {
				// Execute the method
				$this->{$sMethod}();
			}
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method parses a Query string into the response or into an array
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access protected
	 * @param string $sQuery
	 * @param boolean $bUseReturnArray
	 * @return array|FramsieHttp $this
	 */
	protected function parseQueryString($sQuery, $bUseReturnArray = false) {
		// Check to see if we have an array to pipe it into
		if ($bUseReturnArray === true) {
			// Setup the array placeholder
			$aReturn = array();
			// Parse the query string into an array
			parse_str($sQuery, $aReturn);
			// Return the array
			return $aReturn;
		}
		// Parse the query string into the response
		parse_str($sQuery, $this->mResponse);
		// Return the instance
		return $this;
	}

	/**
	 * This method loops through the response hooks and executes them
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access protected
	 * @return FramsieHttp $this
	 */
	protected function executeResponseHooks() {
		// Loop through the response hooks and execute them
		foreach ($this->mResponseHooks as $sMethod) {
			// Check to see if the method has an object associated with it
			if (is_array($sMethod) === true) {
				// Execute the hook
				call_user_func($sMethod);
			} else {
				// Execute the method
				$this->{$sMethod}();
			}
		}
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds a header to the system
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sName
	 * @param string $sValue
	 * @return FramsieHttp $this
	 */
	public function addHeader($sName, $sValue) {
		// Add the header to the system
		$this->mHeaders[$sName] = (string) $sValue;
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a request parameter to the system
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sName
	 * @param multitype $mValue
	 * @param boolean $bEncode
	 * @return FramsieHttp $this
	 */
	public function addParam($sName, $mValue, $bEncode = false) {
		// Check to see if we need to encode the value
		if ($bEncode === true) {
			// Encode based on the data type
			switch ($this->mDataType) {
				case self::DATA_TYPE_JSON : $sValue = (string) json_encode($mValue);                      break; // JSON
				// case self::DATA_TYPE_XMLL : $sValue = (string) FramsieXml::getInstance()->toXml($mValue); break; // XML
			}
		}
		// Add the parameter to the system
		$this->mData[$sName] = $mValue;
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a hook method to the request
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param multitype $mMethod
	 * @throws Exception
	 * @return FramsieHttp $this
	 */
	public function addRequestHook($mMethod) {
		// Make sure the method exists
		$this->ensureHookMethod($mMethod);
		// Add the method
		array_push($this->mRequestHooks, $mMethod);
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a hook method to the response
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param multitype $mMethod
	 * @throws Exception
	 * @return FramsieHttp $this
	 */
	public function addResponseHook($mMethod) {
		// Make sure the method exists
		$this->ensureHookMethod($mMethod);
		// Add the method
		array_push($this->mResponseHooks, $mMethod);
		// Return the instance
		return $this;
	}

	/**
	 * This method makes the request to the remote server and processes the response
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @return FramsieHttp $this
	 */
	public function makeRequest() {
		// Initialize the cURL handler
		$rHandle = curl_init();
		// Determine the request method
		if ($this->mRequestMethod === self::REQUEST_METHOD_GET) {
			// Set the URL
			curl_setopt($rHandle, CURLOPT_URL, (empty($this->mData) ? $this->mUrl : $this->mUrl.'?'.$this->encodeDataParams()));
		} else {
			// Tell the handle that we are making a POST request
			curl_setopt($rHandle, CURLOPT_POST, true);
			// Send the data
			curl_setopt($rHandle, CURLOPT_POSTFIELDS, (empty($this->mData) ? null : $this->encodeDataParams()));
		}
		// Check for a username
		if (!empty($this->mUsername) || !empty($this->mPassword)) {
			// Set the username
			curl_setopt($rHandle, CURLOPT_USERPWD, "{$this->mUsername}:{$this->mPassword}");
			// Tell the handle that we want to authenticate
			curl_setopt($rHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
		// Check for headers
		if (!empty($this->mHeaders)) {
			// Set the headers
			curl_setopt($rHandle, CURLOPT_HTTPHEADER, $this->mHeaders);
		}
		// Tell the handle that we want to return the data
		curl_setopt($rHandle, CURLOPT_RETURNTRANSFER, true);
		// Set the request
		$this->mLastRequest = new stdClass();
		// Set the url
		$this->mLastRequest->sUrl         = (string) $this->mUrl;
		// Set the request data
		$this->mLastRequest->aRequestData = (array) $this->mData;
		// Set the request query string
		$this->mLastRequest->sQueryString = (string) (empty($this->mData) ? null : $this->encodeDataParams());
		// Execute the request hooks
		$this->executeRequestHooks();
		// Set the response placeholder
		$sResponse = null;
		// Try to execute the handle
		try {
			// Execute the handle
			$sResponse = curl_exec($rHandle);
		} catch (Exception $oException) {
			// Initialize the response
			$this->mLastResponse = new stdClass();
			// Set the raw response
			$this->mLastResponse->sResponse = (string) $sResponse;
			// Set the response status
			$this->mLastRespons->bSuccess   = (boolean) false;
			// Set the cURL error
			$this->mLastResponse->sError    = (string) curl_error($rHandle);
			// Set the cURL response code
			$this->mLastResponse->iCode     = (integer) curl_errno($rHandle);
			// Close the handle
			curl_close($rHandle);
			// Return the instance
			return $this;
		}
		// Initialize the response
		$this->mLastResponse            = new stdClass();
		// Set the raw response
		$this->mLastResponse->sResponse = (string) $sResponse;
		// Set the response status
		$this->mLastResponse->bSuccess  = (boolean) true;
		// Set the cURL response code
		$this->mLastResponse->iCode     = (integer) curl_errno($rHandle);
		// Determine the datatype
		switch ($this->mDataType) {
			case self::DATA_TYPE_JSON      : $this->mResponse = json_decode($sResponse, true);                   break; // JSON
			case self::DATA_TYPE_QUERY_STR : $this->parseQueryString($sResponse);                                break; // Query String
			// case self::DATA_TYPE_XML       : $this->mResponse = FramsieXml::getInstance()->toObject($sResponse); break; // XML
			default                        : $this->mResponse = (string) $sResponse;                             break; // HTML|SCRIPT|TEXT
		}
		// Close the handle
		curl_close($rHandle);
		// Process the response hooks
		$this->executeResponseHooks();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current data in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return array
	 */
	public function getData() {
		// Return the current data
		return $this->mData;
	}

	/**
	 * This method returns the current set data type in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return integer
	 */
	public function getDataType() {
		// Return the current data type
		return $this->mDataType;
	}

	/**
	 * This method returns the current headers set in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return array
	 */
	public function getHeaders() {
		// Return the current headers
		return $this->mHeaders;
	}

	/**
	 * This method returns the current HTTP password set in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return string
	 */
	public function getHttpPassword() {
		// Return the current HTTP password
		return $this->mPassword;
	}

	/**
	 * This method returns the current HTTP username set in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return string
	 */
	public function getHttpUsername() {
		// Return the current HTTP username
		return $this->mUsername;
	}

	/**
	 * This method returns the last request in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return stdClass
	 */
	public function getLastRequest() {
		// Return the last request
		return $this->mLastRequest;
	}

	/**
	 * This method returns the last response from the server
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return stdClass
	 */
	public function getLastResponse() {
		// Return the last response
		return $this->mLastResponse;
	}

	/**
	 * This method returns a specific parameter from the data object
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param string $sName
	 * @return multitype
	 */
	public function getParam($sName) {
		// Check for the parameter
		if (empty($this->mData[$sName])) {
			// Return empty
			return null;
		}
		// Return the parameter
		return $this->mData[$sName];
	}

	/**
	 * This method returns the current request method set in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return string
	 */
	public function getRequestMethod() {
		// Return the request method
		return $this->mRequestMethod;
	}

	/**
	 * This method returns the last processed response in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return array
	 */
	public function getResponse() {
		// Return the response from the server
		return $this->mResponse;
	}

	/**
	 * This method returns the current request URL in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return string
	 */
	public function getUrl() {
		// Return the URL
		return $this->mUrl;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the data into the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param array $aData
	 * @return FramsieHttp $this
	 */
	public function setData($aData) {
		// Set the data into the system
		$this->mData = (array) $aData;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the data type into the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param integer $iDataType
	 * @return FramsieHttp $this
	 */
	public function setDataType($iDataType) {
		// Set the data type into the system
		$this->mDataType = (integer) $iDataType;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the headers into the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param array $aHeaders
	 * @return FramsieHttp $this
	 */
	public function setHeaders($aHeaders) {
		// Set the headers into the system
		$this->mHeaders = (array) $aHeaders;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the HTTP password into the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param string $sPassword
	 * @return FramsieHttp $this
	 */
	public function setHttpPassword($sPassword) {
		// Set the password into the system
		$this->mPassword = (string) $sPassword;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the HTTP username into the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param string $sUsername
	 * @return FramsieHttp $this
	 */
	public function setHttpUsername($sUsername) {
		// Set the username into the system
		$this->mUsername = (string) $sUsername;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the request method into the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param integer $iRequestMethod
	 * @return FramsieHttp $this
	 */
	public function setRequestMethod($iRequestMethod) {
		// Set the request method into the system
		$this->mRequestMethod = (integer) $iRequestMethod;
		// Return the instance
		return $this;
	}

	/**
	 * This method validates and sets the URL into the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param string $sUrl
	 * @throws Exception
	 * @return FramsieHttp $this
	 */
	public function setUrl($sUrl) {
		// Validate the URL
		if (filter_var($sUrl, FILTER_VALIDATE_URL) !== false) {
			// Set the URL
			$this->mUrl = (string) $sUrl;
			// Return the instance
			return $this;
		}
		// Throw an exception
		throw new Exception("The URL \"{$sUrl}\" is not a valid URL.");
	}
}