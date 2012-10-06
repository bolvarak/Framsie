<?php
/**
 * This class automatically processes a response from FramsieHttp into an object
 * with nice setters and getters
 * @package Framsie
 * @subpackage FramsieHttpResponseMapper
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieHttpResponseMapper {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the parent of the request section we want
	 * @access protected
	 * @var string|array
	 */
	protected $mParent      = null;

	/**
	 * This property contains the map of original properties to the new global class properties
	 * @access protected
	 * @var array
	 */
	protected $mPropertyMap = array();

	/**
	 * This property contains the actual response
	 * @access protected
	 * @var array|object
	 */
	protected $mResponse    = null;

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor converts an array or object into a class object that has
	 * nice setters and getters
	 * @package Framsie
	 * @subpackage FramsieHttpResponseMapper
	 * @access public
	 * @param array|object $mResponse
	 * @param string|array $mParent
	 * @return FramsieHttpResponseMapper $this
	 */
	public function __construct($mResponse, $mParent = null) {
		// Set the parent into the system
		$this->mParent   = $mParent;
		// Set the response into the system
		$this->mResponse = $mResponse;
		// Process the response to the parent we need
		$this->processResponseToParent();
		// Process the response into getters, properties and setters
		$this->processResponseIntoClass();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Magic Methods ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method magically creates getters and setters to get and set properties
	 * and throws an exception if the method does not exist or is not publically
	 * accessible to the caller
	 * @package Framsie
	 * @subpackage FramsieHttpResponseMapper
	 * @access public
	 * @param string $sMethod
	 * @param array $aValue
	 * @throws Exception
	 * @return multitype
	 */
	public function __call($sMethod, $aArguments) {
		// Check to see if this is a getter
		if (substr(strtolower($sMethod), 0, 3) === 'get') {
			// Set the property name
			$sProperty = (string) 'm'.preg_replace('/get/i', null, $sMethod);
			// Make sure the column exists
			if (in_array($sProperty, array_values($this->mPropertyMap)) === false) {
				// Throw an exception
				throw new Exception("The property \"{$sProperty}\" does not exist in this response object.");
			}
			// Return the property
			return $this->{$sProperty};
		}
		// Check to see if this is a setter
		if (substr(strtolower($sMethod), 0, 3) === 'set') {
			// Set the property name
			$sProperty = (string) 'm'.preg_replace('/set/i', null, $sMethod);
			// Make sure the column exists
			if (in_array($sProperty, array_values($this->mPropertyMap)) === false) {
				// Throw an exception
				throw new Exception("The property \"{$sProperty}\" does not exist in this response object.");
			}
			// Set the property
			$this->{$sProperty} = $aArguments[0];
			// Return the instance
			return $this;
		}
		// If the script gets to this point, throw an exception
		throw new Exception("The method \"{$sMethod}\" does not exist or is not publically accessible.");
	}

	/**
	 * This method throws an exception if a property does not exist or is not
	 * publically accessible to the caller
	 * @package Framsie
	 * @subpackage FramsieHttpResponseMapper
	 * @access public
	 * @param string $sProperty
	 * @throws Exception
	 * @return void
	 */
	public function __get($sProperty) {
		// Make sure the column exists
		if (in_array($sProperty, array_values($this->mPropertyMap)) === false) {
			// Throw an exception
			throw new Exception("The property \"{$sProperty}\" does not exist in this response object.");
		}
		// Return the property
		return $this->{$sProperty};
	}

	/**
	 * This method throws an exception if a property does not exist or is not
	 * publically accessible to the caller
	 * @package Framsie
	 * @subpackage FramsieHttpResponseMapper
	 * @param string $sProperty
	 * @param multitype $mValue
	 * @throws Exception
	 * @return void
	 */
	public function __set($sProperty, $mValue) {
		// Make sure the column exists
		if (in_array($sProperty, array_values($this->mPropertyMap)) === false) {
			// Throw an exception
			throw new Exception("The property \"{$sProperty}\" does not exist in this response object.");
		}
		// Set the property
		$this->{$sProperty} = $mValue;
		// Return the instance
		return $this;
	}

	/**
	 * This method converts the class proeprties back to their original names and JSON encodes them
	 * @package Framsie
	 * @subpackage FramsieHttpResponseMapper
	 * @access public
	 * @return string
	 */
	public function __toString() {
		// Create an array string placeholder
		$aString = array();
		// Loop through the property maps
		foreach ($this->mPropertyMap as $sOriginalProperty => $sGlobalProperty) {
			// Set the value
			$aString[$sOriginalProperty] = $this->{$sGlobalProperty};
		}
		// Return the JSON encoded string
		return json_encode($aString);
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method globalizes the properties in the response into the class object
	 * @package Framsie
	 * @subpackage FramsieHttpResponseMapper
	 * @access protected
	 * @return FramsieHttpResponseMapper $this
	 */
	protected function processResponseIntoClass() {
		// Loop through the response
		foreach ($this->mResponse as $sProperty => $mValue) {
			// Set the original property name
			$sOriginalProperty                      = (string) $sProperty;
			// Set the property name
			$sProperty                              = (string) FramsieConverter::VariableNameToHungarianUpperCamelCase($sProperty);
			// Set the property into the map
			$this->mPropertyMap[$sOriginalProperty] = (string) $sProperty;
			// Set the property into the system
			$this->{$sProperty}                     = FramsieConverter::StringToPhpType($mValue);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method processes the response down to the parent that is needed to populate this object
	 * @package Framsie
	 * @subpackage FramsieHttpResponseMapper
	 * @access protected
	 * @return FramsieHttpResponseMapper $this
	 */
	protected function processResponseToParent() {
		// Check to see if the parent variable is empty
		if (empty($this->mParent) === false) {
			// Determine if the parent is an array
			if (is_array($this->mParent)) {
				// Loop through the parents
				foreach ($this->mParent as $sKey) {
					// Set the response to the proper parent
					$this->mResponse = (is_object($this->mResponse) ? $this->mResponse->{$sKey} : $this->mResponse[$sKey]);
				}
			} else {
				// Set the response to the proper parent
				$this->mResponse = (is_object($this->mResponse) ? $this->mResponse->{$this->mParent} : $this->mResponse[$this->mParent]);
			}
		}
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns a property from the object based on its original name
	 * @package Framsie
	 * @subpackage FramsieHttpResponseMapper
	 * @access public
	 * @param string $sPropertyName
	 * @throws Exception
	 * @return multitype
	 */
	public function getPropertyFromMap($sPropertyName) {
		// Check to see if the property exists
		if (empty($this->mPropertyMap[$sPropertyName])) {
			// The property does not exists
			throw new Exception("The property \"{$sPropertyName}\" does not exist in this response object.");
		}
		// Return the property
		return $this->{$this->mPropertyMap[$sPropertyName]};
	}
}