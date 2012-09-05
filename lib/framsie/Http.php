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
	 * This constant contains the SCRIPT type definition
	 * @var integer
	 */
	const DATA_TYPE_SCRIPT    = 3;

	/**
	 * This constant contains the TEXT type definition
	 * @var integer
	 */
	const DATA_TYPE_TEXT      = 4;

	/**
	 * This constant contains the XML type definition
	 * @var integer
	 */
	const DATA_TYPE_XML       = 5;

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
	 * This property contains the request method
	 * @access protected
	 * @var string
	 */
	protected $mRequestMethod = null;

	/**
	 * This property contains the decoded response from the server
	 * @access protected
	 * @var stdClass
	 */
	protected $mResponse      = null;

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
			curl_setopt($rHandle, CURLOPT_URL, (empty($this->mData) ? $this->mUrl : $this->mUrl.'?'.http_build_query($$this->mData)));
		} else {
			// Tell the handle that we are making a POST request
			curl_setopt($rHandle, CURLOPT_POST, true);
			// Send the data
			curl_setopt($rHandle, CURLOPT_POSTFIELDS, (empty($this->mData) ? null : http_build_query($this->mData)));
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
		$this->mLastRequest->sQueryString = (string) (empty($this->mData) ? null : http_build_query($this->mData));
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
			case self::DATA_TYPE_JSON : $this->mResponse = json_decode($sResponse);                         break; // JSON
			// case self::DATA_TYPE_XML  : $this->mResponse = FramsieXml::getInstance()->toObject($sResponse); break; // XML
			default                   : $this->mResponse = (string) $sResponse;                             break; // HTML|SCRIPT|TEXT
		}
		// Close the handle
		curl_close($rHandle);
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
	 * This method returns the current HTTP password set in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return string
	 */
	public function getPassword() {
		// Return the current HTTP password
		return $this->mPassword;
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
	 * @return stdClass
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

	/**
	 * This method returns the current HTTP username set in the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @return string
	 */
	public function getUsername() {
		// Return the current HTTP username
		return $this->mUsername;
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
	public function setPassword($sPassword) {
		// Set the password into the system
		$this->mPassword = (string) $sPassword;
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

	/**
	 * This method sets the HTTP username into the system
	 * @package Framsie
	 * @subpackage FramsieHttp
	 * @access public
	 * @param string $sUsername
	 * @return FramsieHttp $this
	 */
	public function setUsername($sUsername) {
		// Set the username into the system
		$this->mUsername = (string) $sUsername;
		// Return the instance
		return $this;
	}
}