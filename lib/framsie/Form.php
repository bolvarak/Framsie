<?php
/**
 * This class provides an easy form generation
 * @package Framsie
 * @requires FramsieModel
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
abstract class FramsieForm extends FramsieModel {

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
	 * This property contains the fields for the form
	 * @access protected
	 * @var array
	 */
	protected $mFields         = array();

	/**
	 * This property contains the form's ID
	 * @access protected
	 * @var string
	 */
	protected $mIdentifier     = null;

	/**
	 * This property contains the labels for the elements
	 * @access protected
	 * @var array
	 */
	protected $mLabels         = array();

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
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Magic Methods ////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method builds the form object into HTML and returns it when
	 * the class is echoed out or referenced as a string
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @return string
	 */
	public function __toString() {
		// Return the HTML version of this form
		return $this->getForm();
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method renders the block file for the form
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @access public
	 * @throws FramsieException
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
		if (!file_exists(BLOCK_PATH.DIRECTORY_SEPARATOR.'forms'.DIRECTORY_SEPARATOR.$sFilename)) {
			// Trigger an exception
			FramsieError::Trigger('FRAMBNE', array($sFilename));
		}
		// Start the capture of the output buffer stream
		ob_start();
		// Load the block
		require_once(BLOCK_PATH.DIRECTORY_SEPARATOR.'forms'.DIRECTORY_SEPARATOR.$sFilename);
		// Depending on the print notification either return the buffer
		// or simply print the buffer directly to the screen
		return ob_get_clean();
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method adds an attribute to the form tag
	 * @package Framsie
	 * @subpackage FramsieForm
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
	 * This method adds a field to the form
	 * @package Framsie
	 * @subpackage FramsieForm
	 * @param string $sType
	 * @param string $sName
	 * @param string $sLabel
	 * @param string $sId
	 * @param array $aClasses
	 * @param array $aAttributes
	 * @param array $aDataProvider
	 * @return FramsieFormElement
	 */
	public function addField($sType, $sName, $sLabel, $sId = null, $aClasses = array(), $aAttributes = array(), $aDataProvider = array()) {
		// Create the element object
		$oFormElement = new FramsieFormElement($sName, $sType, $aClasses, $aAttributes);
		// Set the identifier
		$oFormElement->setIdentifier($sId);
		// Set the DataProvider
		$oFormElement->setDataProvider($aDataProvider);
		// Set the element into the instance
		$this->mFields[$sName] = $oFormElement;
		// Add the label
		$this->mLabels[$sName] = (string) $sLabel;
		// Return the instance
		return $this->mFields[$sName];
	}

	/**
	 * This method validates the form
	 * @package Framsie
	 * @subpackage FramsieFormElement
	 * @access public
	 * @return boolean
	 */
	public function isValid() {
		// Create a valid placeholder notification
		$bValid = true;
		// Loop through the fields and set the values
		foreach ($this->mFields as $sName => $oFormElement) {
			// Set the value
			$oFormElement->setValue(Framsie::getInstance()->getRequest()->getParam($sName));
			// Determine if the element is valid
			if ($oFormElement->isValid() === false) {
				// Reset the valid notification
				$bValid = (boolean) false;
			}
		}
		// Return the valid notification
		return $bValid;
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
	 * @throws FramsieException
	 * @return FramsieFormElement|string
	 */
	public function getField($sName) {
		// Make sure the field exists
		if (empty($this->mFields[$sName])) {
			// Trigger an exception
			FramsieError::Trigger('FRAMFNE', array($sName));
		}
		// Return the field instance
		return $this->mFields[$sName];
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
			foreach ($this->mFields as $sName => $oField) {
				// Add the label to the array
				array_push($aFields, $this->getLabel($sName));
				// Add the field to the array
				array_push($aFields, $oField->getHtml());
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
	 * @throws FramsieException
	 * @return string
	 */
	public function getLabel($sName, $bAsHtml = true) {
		// Check for the field
		if (empty($this->mFields[$sName])) {
			// Trigger an exception
			FramsieError::Trigger('FRAMFNE', array($sName));
		}
		// Return the label
		if ($bAsHtml === true) { // Return the HTML formatted label
			return FramsieHtml::getInstance()->getLabel($this->mLabels[$sName], $this->mFields[$sName]->getIdentifier());
		}
		// Return the plain text label
		return $this->mLabels[$sName];
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
