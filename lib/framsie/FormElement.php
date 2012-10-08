<?php
/**
 * This class provides an easy form field generation interface
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieFormElement {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the element's attributes
	 * @access protected
	 * @var array
	 */
	protected $mAttributes        = array();

	/**
	 * This property contains the element's CSS classes
	 * @access protected
	 * @var array
	 */
	protected $mClasses           = array();

	/**
	 * This property contains the element's data provider
	 * @access protected
	 * @var array|object
	 */
	protected $mDataProvider      = null;

	/**
	 * This contains the element's ID attribute
	 * @access protected
	 * @var string
	 */
	protected $mIdentifier        = null;

	/**
	 * This property contains the name of the element
	 * @access protected
	 * @var string
	 */
	protected $mName              = null;

	/**
	 * This property contains the placeholder text for the element
	 * @access protected
	 * @var string
	 */
	protected $mPlaceholder       = null;

	/**
	 * This property tells the system whether or not this element is required
	 * @access protected
	 * @var boolean
	 */
	protected $mRequired          = false;

	/**
	 * This property tells the system what type of element to generate
	 * @access protected
	 * @var string
	 */
	protected $mType              = null;

	/**
	 * This property contains the element's validity notification
	 * @access protected
	 * @var boolean
	 */
	protected $mValid             = true;

	/**
	 * This property contains the validation rules for the field
	 * @access protected
	 * @var array
	 */
	protected $mValidation        = array();

	/**
	 * This property contains the message that is displayed when a field is invalid
	 * @access protected
	 * @var string
	 */
	protected $mValidationMessage = null;

	/**
	 * This property contains an instance of the system validator
	 * @access protected
	 * @var FramsieValidator
	 */
	protected $mValidator         = null;

	/**
	 * This property contains the value for the element
	 * @access protected
	 * @var string
	 */
	protected $mValue             = null;

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constructor sets the element type into the instance
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param string $sName
	 * @param string $sType
	 * @param array $aClasses
	 * @param array $aAttributes
	 * @return FramsieFormElement $this
	 */
	public function __construct($sName, $sType = FramsieHtml::INPUT_TEXT, $aClasses = array(), $aAttributes = array()) {
		// Set the name
		$this->mName       = (string) $sName;
		// Set the element type
		$this->mType       = (string) $sType;
		// Set the classes
		$this->mClasses    = $aClasses;
		// Set the attributes
		$this->mAttributes = $aAttributes;
		// Instantiate the validator
		$this->mValidator = new FramsieValidator();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Magic Methods ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method converts the element to HTML and returns the string
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return string
	 */
	public function __toString() {
		// Return the generated HTML for this element
		return $this->getHtml();
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds an attribute to the element
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param string $sName
	 * @param string $sValue
	 * @return FramsieFormElement $this
	 */
	public function addAttribute($sName, $sValue) {
		// Add the attribute to the array
		$this->mAttributes[$sName] = (string) $sValue;
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a class to the element
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param string $sClass
	 * @return FramsieFormElement $this
	 */
	public function addClass($sClass) {
		// Add the class to the array
		array_push($this->mClasses, $sClass);
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a validation rule to the element
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param string $sPattern
	 * @return FramsieFormElement $this
	 */
	public function addValidationRule($sPattern) {
		// Add the rule to the array
		array_push($this->mValidation, $sPattern);
		// Return the instance
		return $this;
	}

	/**
	 * This method determines whether or not the element is valid
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return boolean
	 */
	public function isValid() {
		// First thing's first, check the value
		if (empty($this->mValue) && ($this->mRequired === true)) {
			// This element is required, yet there is no value, return
			return false;
		}
		// Setup a validation placeholder
		$bValid = true;
		// Loop through the validation rules
		foreach ($this->mValidation as $sRule) {
			// See if the rule validates
			if ($this->mValidator->testPattern($sRule, $this->mValue)) {
				// Append an invalid field class
				array_push($this->mClasses, 'framsieInvalidFormElement');
				// Tell the instance that this element is invalid
				$this->mValid = false;
				// Decline validation
				$bValid       = false;
			}
		}
		// Return the validation results
		return $bValid;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the element's attributes
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return array
	 */
	public function getAttributes() {
		// Return the element's attributes
		return $this->mAttributes;
	}

	/**
	 * This method returns the element's classes
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return array
	 */
	public function getClasses() {
		// Return the element's classes
		return $this->mClasses;
	}

	/**
	 * This method returns the element's data provider
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return array|object
	 */
	public function getDataProvider() {
		// Return the data provider
		return $this->mDataProvider;
	}

	/**
	 * This method returns the element's HTML generated by FramsieHtml
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @uses FramsieHtml
	 * @access public
	 * @return string
	 */
	public function getHtml() {
		// Create an HTML placeholder
		$sHtml = (string) null;
		// Set the classes into the attributes
		$this->mAttributes['class']       = (string) implode(' ', $this->mClasses);
		// Set the placeholder into the attributes
		$this->mAttributes['placeholder'] = (string) $this->mPlaceholder;
		// Determine if the required attribute needs to be set
		if ($this->mRequired === true) {
			// Set the required attribute into the attributes
			// $this->mAttributes['required'] = (string) 'true';
		}
		// Determine the type of input we are generating and generate the HTML
		if ($this->mType === FramsieHtml::INPUT_SELECT) {         // Select
			// Generate the dropdown
			$sHtml = (string) FramsieHtml::getInstance()->getDropdown($this->mName, $this->mIdentifier, $this->mDataProvider, $this->mAttributes, $this->mValue);
		} elseif ($this->mType === FramsieHtml::INPUT_TEXTAREA) { // TextArea
			// Generate the textarea
			$sHtml = (string) FramsieHtml::getInstance()->getTextarea($this->mName, $this->mIdentifier, $this->mValue, $this->mAttributes);
		} else {                                                  // Anything with a valid type for the input tag
			// Add the value to the attributes
			$this->mAttributes['value'] = (string) $this->mValue;
			// Generate the input
			$sHtml = (string) FramsieHtml::getInstance()->getInput($this->mType, $this->mName, $this->mIdentifier, $this->mAttributes);
		}
		// Return the html
		return $sHtml;
	}

	/**
	 * This method returns the element's ID attribute
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return string
	 */
	public function getIdentifier() {
		// Return the element's ID attribute
		return $this->mIdentifier;
	}

	/**
	 * This method returns the element's name
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return string
	 */
	public function getName() {
		// Return the element's name
		return $this->mName;
	}

	/**
	 * This method returns the element's placeholder text
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return string
	 */
	public function getPlaceholder() {
		// Return the element's placeholder
		return $this->mPlaceholder;
	}

	/**
	 * This method returns the element's required status
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return boolean
	 */
	public function getRequired() {
		// Return the element's required status
		return $this->mRequired;
	}

	/**
	 * This method returns the element's type
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return string
	 */
	public function getType() {
		// Return the element's type
		return $this->mType;
	}

	/**
	 * This method returns the validation message
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return string
	 */
	public function getValidationMessage() {
		// Return the validation message
		return $this->mValidationMessage;
	}

	/**
	 * This method returns the element's validation rules
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return array
	 */
	public function getValidationRules() {
		// Return the element's validation rules
		return $this->mValidation;
	}

	/**
	 * This method returns the current FramsieValidator instance
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return FramsieValidator
	 */
	public function getValidator() {
		// Return the validator instance
		return $this->mValidator;
	}

	/**
	 * This method returns the valid notification for the element
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return boolean
	 */
	public function getValidity() {
		// Return the current validation status of the form field
		return $this->mValid;
	}

	/**
	 * This method returns the element's current value
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return string
	 */
	public function getValue() {
		// Return the element's value
		return $this->mValue;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the element's attributes into the instance in one go
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param array $aAttributes
	 * @return FramsieFormElement $this
	 */
	public function setAttributes($aAttributes) {
		// Set the attributes into the instance
		$this->mAttributes = $aAttributes;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the element's classes into the instance in one go
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param array $aClasses
	 * @return FramsieFormElement $this
	 */
	public function setClasses($aClasses) {
		// Set the classes into the system
		$this->mClasses = $aClasses;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the data provider into the element instance
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param array|object $mProvider
	 * @throws FramsieException
	 * @return FramsieFormElement $this
	 */
	public function setDataProvider($mProvider) {
		// Make sure this is an array or an object
		if (is_array($mProvider) || is_object($mProvider)) {
			// Set the data provider into the system
			$this->mDataProvider = $mProvider;
			// Return the instance
			return $this;
		}
		// Trigger an exception
		FramsieError::Trigger('FRAMDPI');
	}

	/**
	 * This method sets the element's id attribute into the instance
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param string $sId
	 * @return FramsieFormElement $this
	 */
	public function setIdentifier($sId) {
		// Set the ID attribute into the element instance
		$this->mIdentifier = (string) $sId;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the element's name into the instance
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param string $sName
	 * @return FramsieFormElement $this
	 */
	public function setName($sName) {
		// Set the name attribute into the element instance
		$this->mName = (string) $sName;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the element's placeholder text into the systme
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param string $sText
	 * @return FramsieFormElement $this
	 */
	public function setPlaceholder($sText) {
		// Set the placeholder text into the element instance
		$this->mPlaceholder = (string) $sText;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the required status of the element
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param boolean $bRequired
	 * @return FramsieFormElement $this
	 */
	public function setRequired($bRequired = true) {
		// Set the element's required status into the instance
		$this->mRequired = (boolean) $bRequired;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the element's type into the instance
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param string $sType
	 * @return FramsieFormElement $this
	 */
	public function setType($sType) {
		// Set the type attribute into the element's instance
		$this->mType = (string) $sType;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the validation message for this element into the instance
	 * @package Framdsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param string $sHtml
	 * @return FramsieFormElement $this
	 */
	public function setValidationMessage($sHtml) {
		// Set the validation message into the instance
		$this->mValidationMessage = (string) $sHtml;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the element's validity into the instance
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param boolean $bValid
	 * @return FramsieFormElement $this
	 */
	public function setValidity($bValid) {
		// Set the valid notification
		$this->mValid = (boolean) $bValid;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the element's value into the instance
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @param multitype $mValue
	 * @return FramsieFormElement $this
	 */
	public function setValue($mValue) {
		// Set the value attribute into the element instance
		$this->mValue = $mValue;
		// Return the instance
		return $this;
	}
}
