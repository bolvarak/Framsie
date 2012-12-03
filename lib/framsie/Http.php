<?php
/**
 * This class provides easy Web requests
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 * @todo Support XML
 */
class FramsieHttp {

  ///////////////////////////////////////////////////////////////////////////
  /// Constants ////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////

  /**
   * This constant contains the HTML type definition
   * @var integer
   */
  const DATA_TYPE_HTML                     = 1;

  /**
   * This constant contains the JSON type definition
   * @var integer
   */
  const DATA_TYPE_JSON                     = 2;

  /**
   * This constant contains the QUERY_STRING type definition
   * @var integer
   */
  const DATA_TYPE_QUERY_STR                = 3;

  /**
   * This constant contains the SCRIPT type definition
   * @var integer
   */
  const DATA_TYPE_SCRIPT                   = 4;

  /**
   * This constant contains the TEXT type definition
   * @var integer
   */
  const DATA_TYPE_TEXT                     = 5;

  /**
   * This constant contains the XML type definition
   * @var integer
   */
  const DATA_TYPE_XML                      = 6;

  /**
   * This constant contains the definition for the JSON format
   * @var string
   */
  const FORMAT_JSON                        = 'json';

  /**
   * This constant contains the definition for the XML format
   * @var string
   */
  const FORMAT_XML                         = 'xml';

  /**
   * This constant contains the definition for the HMAC-SHA1 signature algorithm
   * @var string
   */
  const OAUTH_SIGNATURE_METHOD_HMAC_SHA1   = 'HMAC-SHA1';

  /**
   * This constant contains the version number for OAuth v1.0
   * @var string
   */
  const OAUTH_VERSION_1_0                  = '1.0';

  /**
   * This constant contains the parameter name for the OAuth callback function
   * @var string
   */
  const OAUTH_PARAM_CALLBACK               = 'oauth_callback';

  /**
   * This constant contains the parameter name for the OAuth consumer key
   * @var string
   */
  const OAUTH_PARAM_CONSUMER_KEY           = 'oauth_consumer_key';

  /**
   * This constant contains the parameter name for the OAuth nonce
   * @var string
   */
  const OAUTH_PARAM_NONCE                  = 'oauth_nonce';

  /**
   * This contant contains the parameter name for the OAuth signature
   * @var string
   */
  const OAUTH_PARAM_SIGNATURE              = 'oauth_signature';

  /**
   * This constant contains the parameter name for the OAuth signature method
   * @var string
   */
  const OAUTH_PARAM_SIGNATURE_METHOD       = 'oauth_signature_method';

  /**
   * This constant contains the parameter name for the OAuth timestamp
   * @var string
   */
  const OAUTH_PARAM_TIMESTAMP              = 'oauth_timestamp';

  /**
   * This constant contains the parameter name for the OAuth token
   * @var string
   */
  const OAUTH_PARAM_TOKEN                  = 'oauth_token';

  /**
   * This constant contains the parameter name for the OAuth token secret
   * @var string
   */
  const OAUTH_PARAM_TOKEN_SECRET           = 'oauth_token_secret';

  /**
   * This constant contains the parameter name for the OAuth version
   * @var string
   */
  const OAUTH_PARAM_VERSION                = 'oauth_version';

  /**
   * This constant contains the OAuth parameter name prefix
   * @var string
   */
  const OAUTH_PARAMETER_PREFIX             = 'oauth_';

  /**
   * This constant contains the OpenSocial parameter name prefix
   * @var string
   */
  const OPEN_SOCIAL_PARAMETER_PREFIX       = 'opensocial_';

  /**
   * This constant contains the DELETE type definition for the request
   * @var integer
   */
  const REQUEST_METHOD_DELETE              = 0;

  /**
   * This constant contains the actual string for a DELETE request
   * @var string
   */
  const REQUEST_METHOD_DELETE_NAME         = 'DELETE';

  /**
   * This constant contains the GET type definition for the request
   * @var integer
   */
  const REQUEST_METHOD_GET                 = 1;

  /**
   * This constant contains the actual string for a GET request
   * @var string
   */
  const REQUEST_METHOD_GET_NAME            = 'GET';

  /**
   * This constant contains the POST type definition for the request
   * @var integer
   */
  const REQUEST_METHOD_POST                = 2;

  /**
   * This constant contains the PUT type definition for the request
   * @var integer
   */
  const REQUEST_METHOD_PUT                 = 3;

  /**
   * This constant contains the actual string for a PUT request
   * @var string
   */
  const REQUEST_METHOD_PUT_NAME            = 'PUT';

  /**
   * This constant contains the actual string for a POST request
   * @var string
   */
  const REQUEST_METHOD_POST_NAME           = 'POST';

  /**
   * This constant contains the C variable notator for replacements when building a URL
   * @var string
   */
  const VARIABLE_NOTATOR_C                 = '%s';

  /**
   * This constant contains the Framsie variable notator for replacements when building a URL
   * @var string
   */
  const VARIABLE_NOTATOR_DEFAULT           = ':=';

  /**
   * This constant contains the PDO variable notator for replacements when building a URL
   * @var string
   */
  const VARIABLE_NOTATOR_PDO               = '?';

  /**
   * This constant contains the XOAuth parameter name prefix
   * @var string
   */
  const XOAUTH_PARAMETER_PREFIX            = 'xoauth_';

  ///////////////////////////////////////////////////////////////////////////
  /// Properties ///////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////

  /**
   * This property contains the singleton instance of this class
   * @access protected
   * @staticvar FramsieHttp
   */
  protected static $mInstance              = null;
  
  /**
   * This property contains the current cURL handle
   * @access protected
   * @var resource cURL
   */
  protected $mCurlHandle                   = null;

  /**
   * This property contains the data for the request
   * @access protected
   * @var array
   */
  protected $mData                         = array();

  /**
   * This property contains the hooks that should be executed when the data is encoded
   * @access protected
   * @var array
   */
  protected $mDataEncodeHooks              = array();

  /**
   * This property contains the data type
   * @access protected
   * @var string
   */
  protected $mDataType                     = null;

  /**
   * This property contains the request headers
   * @access protected
   * @var array
   */
  protected $mHeaders                      = array();

  /**
   * This property contains the last sent request
   * @access protected
   * @var stdClass
   */
  protected $mLastRequest                  = null;

  /**
   * This property contains the last server response
   * @access protected
   * @var stdClass
   */
  protected $mLastResponse                 = null;

  /**
   * This method contains the local instance of OAuth
   * @var OAuth
   */
  protected $mOauth                        = null;

  /**
   * This property contains the OAuth consumer key
   * @access protected
   * @var string
   */
  protected $mOauthConsumerKey             = null;

  /**
   * This property contains the OAuth consumer secret
   * @access protected
   * @var string
   */
  protected $mOauthConsumerSecret          = null;

  /**
   * This property contains the OAuth signature method
   * @access protected
   * @var string
   */
  protected $mOauthSignatureMethod         = self::OAUTH_SIGNATURE_METHOD_HMAC_SHA1;

  /**
   * This property contains the OAuth token
   * @access protected
   * @var string
   */
  protected $mOauthToken                   = null;

  /**
   * This property contains the OAuth token secret
   * @access protected
   * @var string
   */
  protected $mOauthTokenSecret             = null;

  /**
   * This property contains the OAuth version that should be used
   * @access protected
   * @var string
   */
  protected $mOauthVersion                 = self::OAUTH_VERSION_1_0;

  /**
   * This property contains the HTTP password
   * @access protected
   * @var string
   */
  protected $mPassword                     = null;

  /**
   * This property contains the raw request data
   * @access protected
   * @var string
   */
  protected $mRawRequestData               = null;

  /**
   * This property contains hooks that should be executed before the request is made
   * @access protected
   * @var arrau
   */
  protected $mRequestHooks                 = array();

  /**
   * This property contains the request method
   * @access protected
   * @var integer
   */
  protected $mRequestMethod                = null;

  /**
   * This property contains the function or method to call when a required parameter is not set
   * @access protected
   * @var array|string
   */
  protected $mRequiredParamMissingCallback = null;

  /**
   * This property contains a list of required fields for the API call
   * @access protected
   * @var array
   */
  protected $mRequiredParams               = array();

  /**
   * This property contains the decoded response from the server
   * @access protected
   * @var array
   */
  protected $mResponse                     = array();

  /**
   * This property contains the hooks that should be executed upon response
   * @access protected
   * @var array
   */
  protected $mResponseHooks                = array();

  /**
   * This property tells the system that the data we send will be a pure POST header
   * @access protected
   * @var boolean
   */
  protected $mSendRawRequest               = false;

  /**
   * This property tells the system whether or not to use OAuth
   * @access protected
   * @var boolean
   */
  protected $mUseOauth                     = false;

  /**
   * This property contains the request url
   * @access protected
   * @var string
   */
  protected $mUrl                          = null;

  /**
   * This property contains the HTTP username
   * @access protected
   * @var string
   */
  protected $mUsername                     = null;

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
   * This method checks the data parameters to ensure all of the required ones are present
   * @package Framsie
   * @package FramsieHttp
   * @access protected
   * @return void
   */
  protected function checkRequiredParams() {
    // First make sure there are params to check
    if (empty($this->mRequiredParams)) {
      // We're done
      return;
    }
    // Loop through the parameters
    foreach ($this->mRequiredParams as $sParameter => $sOrParameter) {
      // Check the parameter and for other parameters
      if (($this->keyExists($sParameter, true) === false) && (is_null($sOrParameter) === false) && ($this->keyExists($sOrParameter, true))) {
        // Check for a callback
        if (empty($this->mRequiredParamMissingCallback) === false) {
          // Execute the callback
          $this->executeRequiredParamMissingCallback($sParameter);
          // We're done
          return;
        } else {
          // Throw an exception
          FramsieError::Trigger('FRAMMRP');
          // We're done
          return;
        }
      }
      // Check for the parameter
      if (($this->keyExists($sParameter, true) === false) && (is_null($sOrParameter) === true)) {
        // Check for a callback
        if (empty($this->mRequiredParamMissingCallback) === false) {
          // Execute the callback
          $this->executeRequiredParamMissingCallback($sParameter);
          // We're done
          return;
        } else {
          // Throw an exception
          FramsieError::Trigger('FRAMMRP');
          // We're done
          return;
        }
      }
    }
    // We're done
    return;
  }

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
      // Check to see if we are using OAuth
      if ($this->mUseOauth === true) {
        // Encode the parameters into OAuth compliance
        return $this->encodeDataParamsOauth($aParameters);
      } else {
        // Encode the custom data array
        return http_build_query($aParameters);
      }
    }
    // Determine if we are using OAuth
    if ($this->mUseOauth === true) {
      // Build an OAuth compliant query string
      $sParams = (string) $this->encodeDataParamsOAuth($this->mData);
    } else {
      // Build a default query string
      $sParams = (string) http_build_query($this->mData);
    }
    // Execute any data encoding hooks
    foreach ($this->mDataEncodeHooks as $mHook) {
      // Determine if the hook is an array
      if (is_array($mHook) === true) {
        // Call the object and method
        $sParams = (string) call_user_func_array($mHook, array($sParams));
      } else {
        // Call the method on this object
        $sParams = (string) call_user_func_array(array($this, $mHook), array($sParams));
      }
    }
    // Return the parameters
    return $sParams;
  }

  /**
   * This method encodes the data parameters into OAuth compliant query strings
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @param array $aParams
   * @return string
   */
  protected function encodeDataParamsOauth($aParams) {
    // Sort the parameters
    ksort($aParams);
    // Create a parameter placeholder
    $aEncodedParams = array();
    // Loop through the parameters
    foreach ($aParams as $sKey => $sValue) {
      // Combine the parameters and add them to the array
      array_push($aEncodedParams, implode('=', array(
        rawurlencode($sKey),  // Set the parameter name
        rawurlencode($sValue) // Set the parameter value
      )));
    }
    // Return the query string
    return implode('&', $aEncodedParams);
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
        call_user_func_array($sMethod, array($this->mCurlHandle));
      } else {
        // Execute the method
        call_user_func_array(array($this, $sMethod), array($this->mCurlHandle));
      }
    }
    // Return the instance
    return $this;
  }

  /**
   * This method executes the callback for missing parameters that are required
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @param string $sParameter
   * @return FramsieHttp $this
   */
  protected function executeRequiredParamMissingCallback($sParameter) {
    // Check to see if the callback is a closure
    if (is_callable($this->mRequiredParamMissingCallback)) {
      // Execute the callback
      $this->mRequiredParamMissingCallback($sParameter);
      // We're done
      return $this;
    }
    // Check to see if the callback is simply a method in this class
    if (is_string($this->mRequiredParamMissingCallback)) {
      // Execute the method
      call_user_func_array(array($this, $this->mRequiredParamMissingCallback), array($sParameter));
      // We're done
      return $this;
    }
    // Execute the callback
    call_user_func_array($this->mRequiredParamMissingCallback, array($sParameter));
    // We're done
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
        call_user_func_array($sMethod, array($this->mResponse));
      } else {
        // Execute the method
        call_user_func_array(array($this, $sMethod), array($this->mResponse));
      }
    }
    // Return the instance
    return $this;
  }

  /**
   * This method initializes the cURL request handle
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @return FramsieHttp $this
   */
  protected function initializeRequestHandle() {
    // Open the resource
    $this->mCurlHandle = curl_init();
    // We want to return transfer
    curl_setopt($this->mCurlHandle, CURLOPT_RETURNTRANSFER, true);
    // We do not wish to verify the peer's SSL
    curl_setopt($this->mCurlHandle, CURLOPT_SSL_VERIFYPEER, false);
    // We do not wish to verify the host's SSL
    curl_setopt($this->mCurlHandle, CURLOPT_SSL_VERIFYHOST, false);
    // We want to follow any redirects
    curl_setopt($this->mCurlHandle, CURLOPT_FOLLOWLOCATION, true);
    // We're done
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
   * This method sets up the cURL handle for a DELETE request
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @return FramsieHttp $this
   */
  protected function setupDeleteRequest() {
    // Set the URL
    curl_setopt($this->mCurlHandle, CURLOPT_URL, (empty($this->mData) ? $this->mUrl : implode('?', array($this->mUrl, $this->encodeDataParams()))));
    // Tell the handle that we are making a DELETE request
    curl_setopt($this->mCurlHandle, CURLOPT_CUSTOMREQUEST, self::REQUEST_METHOD_DELETE_NAME);
    // We're done
    return $this;
  }

  /**
   * This method sets up the cURL handle for a GET request
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @return FramsieHttp $this
   */
  protected function setupGetRequest() {
    // Set the URL
    curl_setopt($this->mCurlHandle, CURLOPT_URL, (empty($this->mData) ? $this->mUrl : implode('?', array($this->mUrl, $this->encodeDataParams()))));
    // We're done
    return $this;
  }

  /**
   * This method sets up the HTTP username and password for the cURL request
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @return FramsieHttp $this
   */
  protected function setupHttpCredentials() {
    // Check for a username
    if (!empty($this->mUsername) || !empty($this->mPassword)) {
      // Set the username
      curl_setopt($this->mCurlHandle, CURLOPT_USERPWD, implode(':', array($this->mUsername, $this->mPassword)));
      // Tell the handle that we want to authenticate
      curl_setopt($this->mCurlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    }
    // We're done
    return $this;
  }

  /**
   * This method sets up the headers for the cURL request
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @return FramsieHttp $this
   */
  protected function setupHttpHeaders() {
    // Check for headers
    if (!empty($this->mHeaders)) {
      // Set the headers
      curl_setopt($this->mCurlHandle, CURLOPT_HTTPHEADER, $this->mHeaders);
    } else {
      // Turn headers off
      curl_setopt($this->mCurlHandle, CURLOPT_HEADER, false);
    }
    // We're done
    return $this;
  }

  /**
   * This method stores the last request data into an object for later reference
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @return FramsieHttp $this
   */
  protected function setupLastRequest() {
    // Set the request
    $this->mLastRequest               = new stdClass();
    // Set the url
    $this->mLastRequest->sUrl         = (string) $this->mUrl;
    // Set the request data
    $this->mLastRequest->aRequestData = (array) $this->mData;
    // Set the request query string
    $this->mLastRequest->sQueryString = (string) (empty($this->mData) ? null : $this->encodeDataParams());
    // Set the cURL information
    $this->mLastRequest->sInfo        = curl_getinfo($this->mCurlHandle);
    // We're done
    return $this;
  }

  /**
   * This method sets up the last response object for later reference
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @param array|object|string $mResponse
   * @param Exception $oException
   * @return FramsieHttp $this
   */
  protected function setupLastResponse($mResponse, $oException = null) {
    // Initialize the response
    $this->mLastResponse             = new stdClass();
    // Set the raw response
    $this->mLastResponse->sResponse  = (string) $mResponse;
    // Set the cURL response code
    $this->mLastResponse->iCode      = (integer) curl_errno($this->mCurlHandle);
    // Set the success code
    $this->mLastResponse->bSuccess   = (boolean) true;
    // Check for an exception
    if (empty($oException) === false) {
      // Set the response status
      $this->mLastRespons->bSuccess    = (boolean) false;
      // Set the cURL error
      $this->mLastResponse->sError     = (string) curl_error($this->mCurlHandle);
      // Set the exception
      $this->mLastResponse->oException = $oException;
    }
    // We're done
    return $this;
  }

  /**
   * This method sets up the cURL handle for a POST request
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @return FramsieHttp $this
   */
  protected function setupPostRequest() {
    // Set the URL
    curl_setopt($this->mCurlHandle, CURLOPT_URL, $this->mUrl);
    // Tell the handle that we are making a POST request
    curl_setopt($this->mCurlHandle, CURLOPT_POST, true);
    // Check to see if we are sending a raw request
    if ($this->mSendRawRequest === true) {
      // Send the data
      curl_setopt($this->mCurlHandle, CURLOPT_POSTFIELDS, $this->mRawRequestData);
    } else {
      // Send the data
      curl_setopt($this->mCurlHandle, CURLOPT_POSTFIELDS, (empty($this->mData) ? null : $this->encodeDataParams()));
    }
    // We're done
    return $this;
  }

  /**
   * This method sets up the cURL handle for a PUT request
   * @package Framsie
   * @subpackage FramsieHttp
   * @access protected
   * @return FramsieHttp $this
   */
  protected function setupPutRequest() {
    // Set the URL
    curl_setopt($this->mCurlHandle, CURLOPT_URL, $this->mUrl);
    // Tell the handle that we are making a PUT request
    curl_setopt($this->mCurlHandle, CURLOPT_CUSTOMREQUEST, self::REQUEST_METHOD_PUT_NAME);
    // Check to see if we are sending a raw request
    if ($this->mSendRawRequest === true) {
      // Send the data
      curl_setopt($this->mCurlHandle, CURLOPT_POSTFIELDS, $this->mRawRequestData);
    } else {
      // Send the data
      curl_setopt($this->mCurlHandle, CURLOPT_POSTFIELDS, (empty($this->mData) ? null : $this->encodeDataParams()));
    }
    // We're done
    return $this;
  }

  ///////////////////////////////////////////////////////////////////////////
  /// Public Methods ///////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////

  /**
   * This method adds a hook method to the data encoding
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param multitype $mMethod
   * @throws Exception
   * @return FramsieHttp $this
   */
  public function addDataEncodeHook($mMethod) {
    // Make sure the method exists
    $this->ensureHookMethod($mMethod);
    // Add the method
    array_push($this->mDataEncodeHooks, $mMethod);
    // Return the instance
    return $this;
  }

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
    array_push($this->mHeaders, "{$sName}:  {$sValue}\n");
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
   * This method adds a required parameter to the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param string $sParam
   * @param string $sOrField
   * @return FramsieHttp $this
   */
  public function addRequriedParam($sParam, $sOrField = null) {
    // Add the field to the system
    $this->mRequiredParams[$sParam] = $sOrField;
    // We're done
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
   * This method builds a request URI from a URI template and variable replacements
   * @package Framsie
   * @subpackage FramsieHtto
   * @param string $sUri
   * @param array $aReplacements
   * @param string $sVariableNotator
   * @throws FramsieException
   * @return string
   */
  public function buildUri($sUri, $aReplacements, $sVariableNotator = self::VARIABLE_NOTATOR_DEFAULT) {
    // Grab the number of occurrences of the notator
    $iOccurrences = substr_count($sUri, $sVariableNotator);
    // Check the number of replacements for too many
    if (count($aReplacements) < $iOccurrences) {
      // Trigger an exception
      FramsieError::Trigger('FRAMTMR');
    }
    // Check the number of replacements for too few
    if (count($aReplacements) > $iOccurrences) {
      // Trigger an exception
      FramsieError::Trigger('FRAMTFR');
    }
    // Loop through the occurrences
    for ($iOccurrence = 0; $iOccurrence < $iOccurrences; $iOccurrence++) {
      // Make the replacement
      $sUri = (string) substr_replace($sUri, rawurlencode($aReplacements[0]), strpos($sUri, $sVariableNotator), strlen($sVariableNotator));
      // Remove the this replacement
      array_shift($aReplacements);
    }
    // Make sure the URL is valid
    if (filter_var($sUri, FILTER_VALIDATE_URL) === false) {
      // Trigger an exception
      FramsieError::Trigger('FRAMIRQ', array($sUri));
    }
    // Return the URI
    return $sUri;
  }

  /**
   * This method checks to see if a key exists in the response
   * @package Framsis
   * @subpackage FramsieHttp
   * @access public
   * @param array|string $mKey
   * @return boolean
   */
  public function keyExists($mKey, $bSearchRequest = false) {
    // Check to see if the key is an array
    if (is_array($mKey)) {
      // Localize the response
      $aSearch = (($bSearchRequest === true) ? $this->mData : $this->mResponse);
      // Loop through the keys
      foreach ($mKey as $sKey) {
        // Check to see of the key exists
        if (array_key_exists($mKey, $aSearch) === false) {
          // We're done
          return false;
        }
        // Update the response
        $aSearch = $aSearch[$mKey];
      }
      // We're done
      return true;
    }
    // Localize the response
    $aSearch = (($bSearchRequest === true) ? $this->mData : $this->mResponse);
    // Check for the key
    if (array_key_exists($mKey, $aSearch) === false) {
      // We're done
      return false;
    }
    // We're done
    return true;
  }

  /**
   * This method checks to see if a response key actually has a value
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param array|string $mKey
   * @return boolean
   */
  public function keyHasValue($mKey) {
    // Check to see if the key is an array
    if (is_array($mKey)) {
      // Localize the response
      $aResponse = $this->mResponse;
      // Loop through the keys
      foreach ($mKey as $sKey) {
        // Check the key for a value
        if (empty($aResponse[$sKey]) && is_null($aResponse[$mKey])) {
          // We're done
          return false;
        }
        // Update the response
        $aResponse = $aResponse[$mKey];
      }
      // We're done
      return true;
    }
    // Check to see if the key has a value
    if (empty($this->mResponse[$mKey]) && is_null($this->mResponse[$mKey])) {
      // We're done
      return false;
    }
    // We're done
    return true;
  }

  /**
   * This method makes the request to the remote server and processes the response
   * @package Framsie
   * @subpackage FramsieHtml
   * @access public
   * @return FramsieHttp $this
   */
  public function makeRequest() {
    // Check to see if we need to initialize OAuth
    if ($this->mUseOauth === true) {
      // Setup OAuth
      $this->setupOauth();
    }
    // Make sure all required fields are present
    $this->checkRequiredParams();
    // Initialize the cURL handle
    $this->initializeRequestHandle();
    // Determine the request method
    switch ($this->mRequestMethod) {
      case self::REQUEST_METHOD_DELETE : $this->setupDeleteRequest(); break; // DELETE
      case self::REQUEST_METHOD_GET    : $this->setupGetRequest   (); break; // GET
      case self::REQUEST_METHOD_POST   : $this->setupPostRequest  (); break; // POST
      case self::REQUEST_METHOD_PUT    : $this->setupPutRequest   (); break; // PUT
      default                          : $this->setupGetRequest   (); break; // Default to GET requests
    }
    // Setup the HTTP username and password
    $this->setupHttpCredentials();
    // Setup the HTTP headers
    $this->setupHttpHeaders();
    // Setup the last request object
    $this->setupLastRequest();
    // Execute the request hooks
    $this->executeRequestHooks();
    // Set the response placeholder
    $sResponse = null;
    // Try to execute the handle
    try {
      // Execute the handle
      $sResponse = curl_exec($this->mCurlHandle);
    } catch (Exception $oException) {
      // Setup the last response
      $this->setupLastResponse($sResponse, $oException);
      // Close the handle
      curl_close($rHandle);
      // Return the instance
      return $this;
    }
    // Setup the last response
    $this->setupLastResponse($sResponse);
    // Determine the datatype
    switch ($this->mDataType) {
      case self::DATA_TYPE_JSON      : $this->mResponse = json_decode($sResponse, true);                   break; // JSON
      case self::DATA_TYPE_QUERY_STR : $this->parseQueryString($sResponse);                                break; // Query String
      // case self::DATA_TYPE_XML       : $this->mResponse = FramsieXml::getInstance()->toObject($sResponse); break; // XML
      default                        : $this->mResponse = (string) $sResponse;                             break; // HTML|SCRIPT|TEXT
    }
    // Close the handle
    curl_close($this->mCurlHandle);
    // Process the response hooks
    $this->executeResponseHooks();
    // Return the instance
    return $this;
  }

  /**
   * This method resets the parameters so that a subsequent call can be made
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return FramsieHttp $this
   */
  public function resetClient() {
    // Reset the parameters
    $this->mData = array();
    // Reset the raw data request
    $this->mRawRequestData = null;
    $this->mSendRawRequest = false;
    // Reset the required fields
    $this->mRequiredParams = array();
    // Return the instance
    return $this;
  }

  /**
   * This method sets up the system for OAuth
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return FramsieHttp $this
   */
  public function setupOauth() {
    // Tell the system to use OAuth
    $this->mUseOauth = true;
    // Add the required parameters
    $this->addParam(self::OAUTH_PARAM_VERSION,        $this->mOauthVersion)         // Send the version
      ->addParam (self::OAUTH_PARAM_NONCE,            $this->getOauthNonce())       // Send the nonce
      ->addParam (self::OAUTH_PARAM_TIMESTAMP,        $this->getOauthTimestamp())   // Send the timestamp
      ->addParam (self::OAUTH_PARAM_SIGNATURE_METHOD, $this->mOauthSignatureMethod) // Send the signature method
      ->addParam (self::OAUTH_PARAM_CONSUMER_KEY,     $this->mOauthConsumerKey);    // Send the consumer key
    // Check for a token
    if (empty($this->mOauthToken) === false) {
      // Set the token into the request
      $this->addParam (self::OAUTH_PARAM_TOKEN, $this->mOauthToken);
    }
    // Check for a token secret
    // if (empty($this->mOauthTokenSecret) === false) {
    //  // Set the token secret into the request
    //  $this->addParam(self::OAUTH_PARAM_TOKEN_SECRET, $this->mOauthTokenSecret);
    // }
    // Set the signature
    $this->addParam(self::OAUTH_PARAM_SIGNATURE, $this->getOauthSignature());
    // Set the authorization header
    // $this->addHeader('Authorization', $this->getOauthAuthorizationHeader());
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
   * This method generates an OAuth authorization header
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return string
   */
  public function getOauthAuthorizationHeader() {
    // Begin the header
    $sHeader      = (string) "OAuth realm=\"{$this->mUrl}\"";
    // Grab the query string
    $sQueryString = (string) $this->encodeDataParamsOauth($this->mData);
    // Explode the query string
    $aQuery       = explode('&', $sQueryString);
    // Loop throug the query
    foreach ($aQuery as $sQueryPair) {
      // Explode the pair
      $aPair = explode('=', $sQueryPair);
      // Append to the header
      $sHeader .= (string) ",{$aPair[0]}=\"{$aPair[1]}\"";
    }
    // Return the header
    return $sHeader;
  }

  /**
   * This method returns the OAuth consumer key
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return string
   */
  public function getOauthConsumerKey() {
    // Return the OAuth consumer key
    return $this->mOauthConsumerKey;
  }

  /**
   * This method returns the OAuth consumer secret
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return string
   */
  public function getOauthConsumerSecret() {
    // Return the OAuth consumer secret
    return $this->mOauthConsumerSecret;
  }

  /**
   * This method is simply a helper that generates an MD5 hash or a unique id
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return string
   */
  public function getOauthNonce() {
    // Return the hash
    return md5(uniqid());
  }

  /**
   * This method returns the OAuth secret key
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return string
   */
  public function getOauthSecretKey() {
    // Generate and the secret key
    $sSecret = (string) implode('&', array(
      $this->mOauthConsumerSecret, // Set the consumer secret
      $this->mOauthTokenSecret     // Set the token secret
    ));
    // Return the secret key
    return $sSecret;
  }

  /**
   * This method returns the OAuth signature for the request
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return string
   */
  public function getOauthSignature() {
    // Set the signature placeholder
    $sSignature = (string) null;
    // Set the method placeholder
    $sMethod    = (string) null;
    // Determine the method
    switch ($this->mRequestMethod) {
      // GET
      case self::REQUEST_METHOD_GET  : $sMethod = self::REQUEST_METHOD_GET_NAME;  break;
      // POST
      case self::REQUEST_METHOD_POST : $sMethod = self::REQUEST_METHOD_POST_NAME; break;
    }
    // Create the signature base
    $sSignature = (string) implode('&', array(
      $sMethod,
      rawurlencode($this->mUrl),
      rawurlencode($this->encodeDataParamsOauth($this->mData))
    ));
    // Determine the encryption to run
    switch ($this->mOauthSignatureMethod) {
      // HMAC-SHA1
      case self::OAUTH_SIGNATURE_METHOD_HMAC_SHA1 : return base64_encode(hash_hmac('sha1', $sSignature, $this->getOauthSecretKey(), true)); break;
    }
  }

  /**
   * This method returns the current seconds since epoch
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return integer
   */
  public function getOauthTimestamp() {
    // Return the timestamp
    return time();
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
   * This method returns the current raw request data
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param boolean $bDecode
   * @return string
   */
  public function getRawRequestData($bDecode = true) {
    // Return the raw request data
    return (($bDecode === true) ? rawurldecode($this->mRawRequestData) : $this->mRawRequestData);
  }

  /**
   * This method returns the current request method set in the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return integer
   */
  public function getRequestMethod() {
    // Return the request method
    return $this->mRequestMethod;
  }

  /**
   * This method returns the callback hook for the missing parameters check
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return array|closure
   */
  public function getRequiredParamMissingCallback() {
    // Return the callback
    return $this->mRequiredParamMissingCallback;
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
   * This method returns the boolean notification for sending raw requests
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @return boolean
   */
  public function getSendRawRequest() {
    // Return the raw request notification
    return $this->mSendRawRequest;
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
   * This method sets the OAuth consumer key into the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param string $sConsumerKey
   * @return FramsieHttp $this
   */
  public function setOauthConsumerKey($sConsumerKey) {
    // Set the OAuth consumer key
    $this->mOauthConsumerKey = (string) $sConsumerKey;
    // Return the instance
    return $this;
  }

  /**
   * This method sets the OAuth consumer secret into the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param string $sConsumerSecret
   * @return FramsieHttp $this
   */
  public function setOauthConsumerSecret($sConsumerSecret) {
    // Set the OAuth consumer secret
    $this->mOauthConsumerSecret = (string) $sConsumerSecret;
    // Return the instance
    return $this;
  }

  /**
   * This method sets the OAuth signature method into the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param string $sMethod
   * @return FramsieHttp $this
   */
  public function setOauthSignatureMethod($sMethod) {
    // Set the OAuth signature method
    $this->mOauthSignatureMethod = (string) $sMethod;
    // Return the instance
    return $this;
  }

  /**
   * This method sets the OAuth token into the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param string $sToken
   * @return FramsieHttp $this
   */
  public function setOauthToken($sToken) {
    // Set the OAuth token
    $this->mOauthToken = (string) $sToken;
    // Return the instance
    return $this;
  }

  /**
   * This method sets the OAuth token secret into the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param string $sTokenSecret
   * @return FramsieHttp $this
   */
  public function setOauthTokenSecret($sTokenSecret) {
    // Set the OAuth token secret
    $this->mOauthTokenSecret = (string) $sTokenSecret;
    // Return the instance
    return $this;
  }

  /**
   * This method sets the OAuth version into the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param string $sVersion
   * @return FramsieHttp $this
   */
  public function setOauthVersion($sVersion) {
    // Set the OAuth version
    $this->mOauthVersion = (string) $sVersion;
    // Return the instance
    return $this;
  }

  /**
   * This method sets the raw request data into the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param string $sData
   * @return FramsieHttp $this
   */
  public function setRawRequestData($sData) {
    // Determine the datatype
    switch ($this->mDataType) {
      case self::DATA_TYPE_JSON      : $this->mRawRequestData = (string) json_encode($sData);      break; // JSON
      case self::DATA_TYPE_QUERY_STR : $this->mRawRequestData = (string) http_build_query($sData); break; // Query String
         // case self::DATA_TYPE_XML       : $this->mResponse = FramsieXml::getInstance()->toObject($sResponse); break; // XML
      default                        : $this->mRawRequestData = (string) $sData;                   break; // HTML|SCRIPT|TEXT
    }
    // We're done
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
   * This method adds a callback to the required parameters check hook
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param closure|string $mCallback
   * @param object|string $mObject
   * @return FramsieHttp $this
   */
  public function setRequiredParamMissingCallback($mCallback, $mObject = null) {
    // First check to see if they provided a literal callback
    if (is_callable($mCallback)) {
      // Set the callback into the system
      $this->mRequiredParamMissingCallback = $mCallback;
      // We're done
      return $this;
    }
    // Check to see if the method or function they provided exists
    if (method_exists($this, $mCallback) || function_exists($mCallback)) {
      // Set the callback into the system
      $this->mRequiredParamMissingCallback = $mCallback;
      // We're done
      return $this;
    }
    // Check to see if an object was provided with the method
    if (is_null($mObject) === false) {
      // Check to see if object provided is a string or instance
      if (is_string($mObject) && class_exists($mObject)) {
        // Instantiate the object
        $mObject = new $mObject();
      }
      // Check to see if the method exists in the object
      if (method_exists($mObject, $mCallback)) {
        // Set the callback into the system
        $this->mRequiredParamMissingCallback = array($mObject, $mCallback);
        // We're done
        return $this;
      }
    }
    // If we get to this point, trigger an exception
    FramsieError::Trigger('FRAMICS');
  }

  /**
   * This method sets the notification in the system that tells it we are sending raw request data
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param boolean $bSendRaw
   * @return FramsieHttp $this
   */
  public function setSendRawRequest($bSendRaw = true) {
    // Set the notification into the system
    $this->mSendRawRequest = (boolean) $bSendRaw;
    // We're done
    return $this;
  }

  /**
   * This method tells the system whether or not to use OAuth
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param boolean $bUseOauth
   * @return FramsieHttp $this
   */
  public function setUseOauth($bUseOauth) {
    // Set the OAuth enabler
    $this->mUseOauth = (boolean) $bUseOauth;
    // Return the instance
    return $this;
  }

  /**
   * This method validates and sets the URL into the system
   * @package Framsie
   * @subpackage FramsieHttp
   * @access public
   * @param string $sUrl
   * @throws FramsieException
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
    FramsieError::Trigger('FRAMIRQ', array($sUrl));
  }
}
