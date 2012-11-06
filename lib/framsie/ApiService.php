<?php

/**
 * This class provides an easy interface for setting API endpoints
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
abstract class FramsieApiService {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the transfer method for GET
	 * @var integer
	 */
	const METHOD_GET           = 0;

	/**
	 * This constant contains the transfer method for POST
	 * @var integer
	 */
	const METHOD_POST          = 1;

	/**
	 * This constant contains the transfer methof got PUT
	 * @var unknown_type
	 */
	const METHOD_PUT           = 2;

	/**
	 * This constant contains the data transfer method for HTML
	 * @var integer
	 */
	const TRANSFER_HTML        = 0;

	/**
	 * This constant contains the data transfer method for JSON
	 * @var integer
	 */
	const TRANSFER_JSON        = 1;

	/**
	 * This constant contains the data transfer method for scripts
	 * @var integer
	 */
	const TRANSFER_SCRIPT      = 2;

	/**
	 * This constant contains the data transfer method for text
	 * @var integer
	 */
	const TRANSFER_TEXT        = 3;

	/**
	 * This constant contains the data transfer method for XML
	 * @var integer
	 */
	const TRANSFER_XML         = 4;

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the arguments POST/GET request key
	 * @access protected
	 * @var string
	 */
	protected $mArgumentRequestKey = null;

	/**
	 * This property contains the base POST/GET request key
	 * @access protected
	 * @var string
	 */
	protected $mBaseRequestKey     = null;

	/**
	 * This property contains the data type transfer for this service
	 * @access protected
	 * @var integer
	 */
	protected $mDataType           = 1;

	/**
	 * This property contains the AJAX endpoint for this service
	 * @access protected
	 * @var string
	 */
	protected $mEndpoint           = null;

	/**
	 * This property contains the method POST/GET requst key
	 * @access protected
	 * @var string
	 */
	protected $mMethodRequestKey   = null;

	/**
	 * This property contains the methods that are valid for this service
	 * @access protected
	 * @var array
	 */
	protected $mMethods            = array();

	/**
	 * This property contains the transfer method for this service
	 * @access protected
	 * @var integer
	 */
	protected $mTransferMethod     = 1;

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////




	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////




	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds an endpoint method to the system
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @param string $sMethod
	 * @param integer $iExpectedArguments
	 * @param object $oParent
	 * @throws FramsieException
	 * @return FramsieApiService $this
	 */
	public function addMethod($sMethod, $iExpectedArguments = null, $oParent = null) {
		// Check for a parent and if the method exists
		if ((empty($oParent) === false) && (method_exists($oParent, $sMethod) === false)) {
			// Trigger an exception
			FramsieError::Trigger('FRAMAMI', array($sMethod));
		}
		// Check to see if the method exists
		if (method_exists($this, $sMethod) === false) {
			// Trigger an exception
			FramsieError::Trigger('FRAMAMI', array($sMethod));
		}
		// Set the method into the system
		$this->mMethods[$sMethod] = array(
			'iExpectedArguments' => (integer) $iExpectedArguments,
			'oParent'            => (object)  $oParent
		);
		// Return the instance
		return $this;
	}

	/**
	 * This method adds an endpont method that is a simple callback/closure to the system
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @param string $sMethod
	 * @param function $fCallback
	 * @param integer $iExpectedArguments
	 * @throws FramsieException
	 * @return FramsieApiService $this
	 */
	public function addMethodCallback($sMethod, $fCallback, $iExpectedArguments = null) {
		// Make sure the method has a callback
		if (is_callable($fCallback) === false) {
			// Trigger an exception
			FramsieError::Trigger('FRAMACI', array($sMethod));
		}
		// Set the method into the system
		$this->mMethods[$sMethod] = array(
			'fCallback'          => $fCallback,
			'iExpectedArguments' => (integer) $iExpectedArguments
		);
		// Return the instance
		return $this;
	}


	public function sendEndpointResponse($mResponse) {
		// Create a response placeholder
		$sResponse = null;
		// Determine the endpoint data type
		switch ($this->mDataType) {
			// JSON
			case self::TRANSFER_JSON   : $sResponse = (string) json_encode($mResponse); break;
			// XML
			case self::TRANSFER_XML    : $sResponse = (string) $mResponse;              break;
			// HTML
			case self::TRANSFER_HTML   :
			// Script
			case self::TRANSFER_SCRIPT :
			// Text
			case self::TRANSFER_TEXT   :
			// Default
			default                    : $sResponse = (string) $mResponse;              break;
		}
		// Send the response
		die($sResponse);
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the argument request key
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @return string
	 */
	public function getArgumentRequestKey() {
		// Return the argument request key
		return $this->mArgumentRequestKey;
	}

	/**
	 * This method returns the base request key
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @return string
	 */
	public function getBaseRequestKey() {
		// Return the base request key
		return $this->mBaseRequestKey;
	}

	/**
	 * This method returns the system's accepted data type
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @return integer
	 */
	public function getDataType() {
		// Return the service's data type
		return $this->mDataType;
	}

	/**
	 * This method returns the current endpoint
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @return string
	 */
	public function getEndpoint() {
		// Return the endpoint
		return $this->mEndpoint;
	}

	/**
	 * This method returns the system's method request key
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @return string
	 */
	public function getMethodRequestKey() {
		// Return the service's request method key
		return $this->mMethodRequestKey;
	}

	/**
	 * This method returns the service's transfer method
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @return integer
	 */
	public function getTransferMethod() {
		// Return the system's transfer method
		return $this->mTransferMethod;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the argument request key into the system
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @param string $sKey
	 * @return FramsieApiService $this
	 */
	public function setArgumentRequestKey($sKey) {
		// Set the argument request key
		$this->mArgumentRequestKey = (string) $sKey;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the base request key into the system
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @param string $sKey
	 * @return FramsieApiService $this
	 */
	public function setBaseRequestKey($sKey) {
		// Set the base request key
		$this->mBaseRequestKey = (string) $sKey;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the data type into the system
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @param integer $iDataType
	 * @return FramsieApiService $this
	 */
	public function setDataType($iDataType) {
		// Set the data type
		$this->mDataType = (integer) $iDataType;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the AJAX endpoint for this service
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @param string $sEndpoint
	 * @return FramsieApiService $this
	 */
	public function setEndpoint($sEndpoint) {
		// Set the endpoint for this AJAX service
		$this->mEndpoint = (string) $sEndpoint;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the method key into the system
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @param string $sKey
	 * @return FramsieApiService $this
	 */
	public function setMethodRequestKey($sKey) {
		// Set the method request key into the system
		$this->mMethodRequestKey = (string) $sKey;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the data transfer method into the system
	 * @package Framsie
	 * @subpackage FramsieApiService
	 * @access public
	 * @param integer $iMethod
	 * @return FramsieApiService $this
	 */
	public function setTransferMethod($iMethod) {
		// Set the transfer method into the system
		$this->mTransferMethod = (integer) $iMethod;
		// Return the instance
		return $this;
	}
}
