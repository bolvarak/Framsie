<?php
/**
 * This class provides an easy form generation
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
abstract class FramsieForm {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the value for a GET request form
	 * @var string
	 */
	const METHOD_GET          = 'get';

	/**
	 * This constant contains the value for a POST request form
	 * @var string
	 */
	const METHOD_POST         = 'post';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the form's action
	 * @access protected
	 * @var string
	 */
	protected $mAction         = null;

	/**
	 * This property contains the form's attributes
	 * @access protected
	 * @var array
	 */
	protected $mAttributes     = array('enctype' => 'multipart/form-data');

	/**
	 * This property contains the block file for the form's display
	 * @access protected
	 * @var string
	 */
	protected $mBlockFile      = null;

	/**
	 * This property contains the form's class
	 * @access protected
	 * @var string
	 */
	protected $mClass          = null;

	/**
	 * This property contains the data providers for select fields
	 * @access protected
	 * @var stdClass
	 */
	protected $mDataProviders  = null;

	/**
	 * This property contains the fields for the form
	 * @access protected
	 * @var stdClass
	 */
	protected $mFields         = null;

	/**
	 * This property contains the form's ID
	 * @access protected
	 * @var string
	 */
	protected $mIdentifier     = null;

	/**
	 * This property contains the request method for the form
	 * @access protected
	 * @var string
	 */
	protected $mMethod         = self::METHOD_POST;

	/**
	 * This property contains the name of the form
	 * @access protected
	 * @var string
	 */
	protected $mName           = null;

	/**
	 * This property contains the selected values for dropdowns
	 * @access protected
	 * @var stdClass
	 */
	protected $mSelectedValues = null;

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor initializes the fields and datasources objects
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return FramsieForm $this
	 */
	public function __construct() {
		// Initialize the fields
		$this->mFields         = new stdClass();
		// Initialize the data providers
		$this->mDataProviders  = new stdClass();
		// Initialize the selected values
		$this->mSelectedValues = new stdClass();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method renders the block file for the form
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return string
	 */
	public function renderBlock() {
		// Set the filename
		$sFilename = (string) $this->mBlockFile;
		// Check for an extension
		if (!preg_match('/\.css|js|php|phtml$/i', $sFilename)) {
			// Append the file extension to the filename
			$sFilename .= (string) "{$sFilename}.phtml";
		}
		// Make sure the file exists
		if (!file_exists(BLOCK_PATH."/forms/{$sFilename}")) {
			// Throw an exception because if this method is called, obviously
			// the block is needed to continue
			throw new Exception("The block file \"{$sFilename}\" does not exist as it was called, nor does it exist in the blocks directory");
		}
		// Start the capture of the output buffer stream
		ob_start();
		// Load the block
		require_once(BLOCK_PATH."/forms/{$sFilename}");
		// Depending on the print notification either return the buffer
		// or simply print the buffer directly to the screen
		return ob_get_clean();
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds an attribute to the system
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sName
	 * @param string $sValue
	 * @return FramsieForm $this
	 */
	public function addAttribute($sName, $sValue) {
		// Add the attribute to the system
		$this->mAttributes[$sName] = (string) $sValue;
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a dataprovider for a select field
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sName
	 * @param array $aDataProvider
	 * @throws Exception
	 * @return FramsieForm $this
	 */
	public function addDataProvider($sName, $aDataProvider) {
		// Make sure the field exists
		if (empty($this->mFields->{$sName})) {
			// Throw an exception
			throw new Exception("The form field \"{$sName}\" does not exist.");
		}
		// Set the data provider
		$this->mDataProviders->{$sName} = $aDataProvider;
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a field to the form
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @param string $sType
	 * @param string $sName
	 * @param string $sLabel
	 * @param string $sId
	 * @param string $sClass
	 * @param array $aAttributes
	 * @param array $aDataProvider
	 * @param string $sSelected
	 * @return FramsieForm $this
	 */
	public function addField($sType, $sName, $sLabel, $sId = null, $sClass = null, $aAttributes = array(), $aDataProvider = array(), $sSelected = null) {
		// Check for a class
		if (!empty($sClass)) {
			// Set the class into the attributes
			$aAttributes['class'] = (string) $sClass;
		}
		// Add the element to the system
		$this->mFields->{$sName} = new stdClass();
		// Add the attributes
		$this->mFields->{$sName}->aAttributes = (array) $aAttributes;
		// Add the identifier
		$this->mFields->{$sName}->sIdentifier = (string) (empty($sId) ? $sName : $sId);
		// Set the label
		$this->mFields->{$sName}->sLabel      = (string) $sLabel;
		// Set the type
		$this->mFields->{$sName}->sType       = (string) $sType;
		// Check for a data provider
		if (empty($aDataProvider) === false) {
			// Add the data provider
			$this->addDataProvider($sName, $aDataProvider);
		}
		// Check for a selected value
		if (empty($sSelected) === false) {
			// Add the selected value
			$this->addSelectedValue($sName, $sSelected);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a selected value to the dropdown
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sName
	 * @param string $sValue
	 * @throws Exception
	 * @return FramsieForm $this
	 */
	public function addSelectedValue($sName, $sValue) {
		// Check for the field
		if (empty($this->mFields->{$sName})) {
			// Throw an exception
			throw new Exception("The field \"{$sName}\" does not exist.");
		}
		// Set the selected value
		$this->mSelectedValues->{$sName} = $sValue;
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a validation pattern to the selected field
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sName
	 * @param string $sScheme
	 * @param string $sMessage
	 * @throws Exception
	 * @return FramsieForm $this
	 */
	public function addValidator($sName, $sScheme, $sMessage) {
		// Check for the field
		if (empty($this->mFields->{$sName})) {
			// Throw an exception
			throw new Exception("The field \"{$sName}\" does not exist.");
		}
		// Set the validation pattern
		$this->mFields->{$sName}->sValidationPattern = (string) $sScheme;
		// Set the validation notifier
		$this->mFields->{$sName}->bValid             = (boolean) true;
		// Set the validation message
		$this->mFields->{$sName}->sMessage           = (string) $sMessage;
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the current form's action
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return string
	 */
	public function getAction() {
		// Return the current action
		return $this->mAction;
	}

	/**
	 * This method returns the current form attributes
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return array
	 */
	public function getAttributes() {
		// Return the current form attributes
		return $this->mAttributes;
	}

	/**
	 * This method returns the current form's block file
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return string
	 */
	public function getBlockFile() {
		// Return the form's block file
		return $this->mBlockFile;
	}

	/**
	 * This method returns a single form field and its label
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sName
	 * @param bool $bAsHtml
	 * @throws Exception
	 * @return string
	 */
	public function getField($sName, $bAsHtml = true) {
		// Check for the field
		if (empty($this->mFields->{$sName})) {
			// Throw a new exception
			throw new Exception("The field \"{$sName}\" does not exist.");
		}
		// Determine the field type
		switch($this->mFields->{$sName}->sType) {
			// Select
			case FramsieHtml::INPUT_SELECT :
				// Return the field
				return FramsieHtml::getInstance()->getDropdown(
					$sName,                                                                                                                          // Send the name
					$this->mFields->{$sName}->sIdentifier,                                                                                           // Send the identifier
					(empty($this->mDataProviders->{$sName}) ? array() : $this->mDataProviders->{$sName}),                                            // Send the data provider
					$this->mFields->{$sName}->aAttributes,                                                                                           // Send the attributes
					(empty($this->mSelectedValues->{$sName}) ? null   : (empty($_POST[$sName]) ? $this->mSelectedValues->{$sName} : $_POST[$sName])) // Send the selected value
				);
			// We're done
			break;

			// Textarea
			case FramsieHtml::INPUT_TEXTAREA :
				// Return the field
				return FramsieHtml::getInstance()->getTextarea(
					$sName,                                          // Send the name
					$this->mFields->{$sName}->sIdentifier,           // Send the identifier
					(empty($_POST[$sName]) ? null : $_POST[$sName]), // Send the content
					$this->mFields->{$sName}->aAttributes            // Send the attributes
				);
			// We're done
			break;

			// Everything else
			default :
				// Check for POST data
				if (empty($_POST[$sName]) === false) {
					// Set the field value
					$this->mFields->{$sName}->aAttributes['value'] = $_POST[$sName];
				}
				// Return the field
				return FramsieHtml::getInstance()->getInput(
					$this->mFields->{$sName}->sType,       // Send the input type
					$sName,                                // Send the name
					$this->mFields->{$sName}->sIdentifier, // Send the identifier
					$this->mFields->{$sName}->aAttributes  // Send the attributes
				);
			break;
		}
	}

	/**
	 * This method returns the form HTML
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return string
	 */
	public function getForm() {
		// Check for a block file
		if (empty($this->mBlockFile)) {
			// Create the fields HTML placeholder
			$aFields = array();
			// Loop through the fields
			foreach ($this->mFields as $sName => $oProperties) {
				// Add the label to the array
				array_push($aFields, $this->getLabel($sName));
				// Add the field to the array
				array_push($aFields, $this->getField($sName));
			}
			// Now generate and return the entire form
			return FramsieHtml::getInstance()->getForm(
				$this->mAction,     // Send the action
				$this->mMethod,     // Send the method
				$this->mName,       // Send the name
				$this->mIdentifier, // Send the identifier
				$aFields,           // Send the fields
				$this->mAttributes  // Send the attributes
			);
		}
		// Load the block file
		return $this->renderBlock();
	}

	/**
	 * This method returns the opening form tag for this form
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return string
	 */
	public function getFormTag() {
		// Return the opening form tag
		return "<form action=\"{$this->mAction}\" class=\"{$this->mClass}\" enctype=\"multipart/form-data\" id=\"{$this->mIdentifier}\" method=\"{$this->mMethod}\" name=\"{$this->mName}\">";
	}

	/**
	 * This method returns the closing form tag
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return string
	 */
	public function getFormTagClose() {
		// Return the closing form tag
		return "</form>";
	}

	/**
	 * This method returns the label for the selected field
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sName
	 * @param boolean $bAsHtml
	 * @throws Exception
	 * @return string
	 */
	public function getLabel($sName, $bAsHtml = true) {
		// Check for the field
		if (empty($this->mFields->{$sName})) {
			// Throw an exception
			throw new Exception("The field \"{$sName}\" does not exist.");
		}
		// Return the label
		if ($bAsHtml === true) { // Return the HTML formatted label
			return FramsieHtml::getInstance()->getLabel($this->mFields->{$sName}->sLabel, $this->mFields->{$sName}->sIdentifier);
		}
		// Return the plain text label
		return $this->mFields->{$sName}->sLabel;
	}

	/**
	 * This method returns the current form's request method
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return string
	 */
	public function getMethod() {
		// Return the current method
		return $this->mMethod;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the form's action into the system
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sUrl
	 * @return FramsieForm $this
	 */
	public function setAction($sUrl) {
		// Set the action into the system
		$this->mAction = (string) $sUrl;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the attributes array into the system
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param array $aAttributes
	 * @return FramsieForm $this
	 */
	public function setAttributes($aAttributes) {
		// Set the attributes into the system
		$this->mAttributes = (array) $aAttributes;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the form's block file into the system
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sFile
	 * @return FramsieForm $this
	 */
	public function setBlockFile($sFile) {
		// Set the block file into the system
		$this->mBlockFile = (string) $sFile;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the form's class into the system
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sClass
	 * @return FramsieForm $this
	 */
	public function setClass($sClass) {
		// Set the cladd into the system
		$this->mClass = (string) $sClass;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the form's unique identifier
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sId
	 * @return FramsieForm $this
	 */
	public function setIdentifier($sId) {
		// Set the identifier into the system
		$this->mIdentifier = (string) $sId;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the form's request method into the system
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sMethod
	 * @return FramsieForm $this
	 */
	public function setMethod($sMethod) {
		// Set the method into the system
		$this->mMethod = (string) $sMethod;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the form's name into the system
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @param string $sName
	 * @return FramsieForm $this
	 */
	public function setName($sName) {
		// Set the name into the system
		$this->mName = (string) $sName;
		// Return the instance
		return $this;
	}
}