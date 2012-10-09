<?php
/**
 * This class provides generators for HTML entities
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieHtml {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the type value for a button input tag
	 * @var string
	 */
	const INPUT_BUTTON        = 'button';

	/**
	 * This constant contains the type value for a checkbox input tag
	 * @var string
	 */
	const INPUT_CHECKBOX      = 'checkbox';

	/**
	 * This constant contains the type value for a file input tag
	 * @var string
	 */
	const INPUT_FILE          = 'file';

	/**
	 * This constant contains the type value for a hidden input tag
	 * @var string
	 */
	const INPUT_HIDDEN        = 'hidden';

	/**
	 * This constant contains the type value for a image input tag
	 * @var string
	 */
	const INPUT_IMAGE         = 'image';

	/**
	 * This constant contains the type value for a password input tag
	 * @var string
	 */
	const INPUT_PASSWORD      = 'password';

	/**
	 * This constant contains the type value for a radio input
	 * @var string
	 */
	const INPUT_RADIO         = 'radio';

	/**
	 * This constant contains the type value for a reset button
	 * @var string
	 */
	const INPUT_RESET         = 'reset';

	/**
	 * This constant contains the select tag name
	 * @var string
	 */
	const INPUT_SELECT        = 'select';

	/**
	 * This constant contains the type value for a submit input
	 * @var string
	 */
	const INPUT_SUBMIT        = 'submit';

	/**
	 * This constant contains the type value for a text input
	 * @var string
	 */
	const INPUT_TEXT          = 'text';

	/**
	 * This constant contains textarea tag name
	 * @var string
	 */
	const INPUT_TEXTAREA      = 'textarea';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of this class
	 * @access protected
	 * @staticvar FramsieHtml
	 */
	protected static $mInstance = null;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method grabs the singleton instance from the system
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieHtml self::$mInstance
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
	 * This method sets an external instance into this class
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @static
	 * @param FramsieHtml $oInstance
	 * @return FramsieHtml self::$mInstance
	 */
	public static function setInstance(FramsieHtml $oInstance) {
		// Set the external instance into this class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply returns the instance and is protected to enforce
	 * the singleton pattern throughout the system
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access protected
	 * @return FramsieHtml $this
	 */
	protected function __constructor() {
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method processes the attributes array for the element being generated
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access protected
	 * @param array $aAttributes
	 * @return string
	 */
	protected function processAttributes($aAttributes) {
		// Start the attributes
		$sAttributes = (string) null;
		// Loop through each of the attributes
		foreach ($aAttributes as $sName => $sAttributeValue) {
			// Append the attribute
			$sAttributes .= (string) "{$sName}=\"{$sAttributeValue}\" ";
		}
		// Return the attribute string
		return $sAttributes;
	}

	/**
	 * This method processes the child elements of the element currently being generated
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access protected
	 * @param array $aElements
	 * @return string
	 */
	protected function processElements($aElements) {
		// Create an element string placeholder
		$sElements = (string) null;
		// Loop through the elements
		foreach ($aElements as $sElement) {
			// Append the element
			$sElements .= (string) $sElement;
		}
		// Return the elements string
		return $sElements;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method generates a dropdown data provider from a database lookup table
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @param string $sLookupTable
	 * @param string $sValueField
	 * @param string $sLabelField
	 * @param boolean $bAddPlaceholder
	 * @return array
	 */
	public function generateDataProvider($sLookupTable, $sValueField, $sLabelField, $bAddPlaceholder = true) {
		// Create the placeholder array
		$aDataProvider = array();
		// Check to see if we should add a placeholder
		if ($bAddPlaceholder === true) {
			$aDataProvider['Select...'] = null;
		}
		// Setup the DBI
		FramsieDatabaseInterface::getInstance(true)
			->setTable($sLookupTable)
			->setQuery(FramsieDatabaseInterface::SELECTQUERY)
			->addField($sValueField)
			->addField($sLabelField)
			->generateQuery();
		// Fetch and loop through the resultd
		foreach (FramsieDatabaseInterface::getInstance()->getRows(PDO::FETCH_OBJ) as $oRow) {
			// Append the value
			$aDataProvider[$oRow->{$sLabelField}] = $oRow->{$sValueField};
		}
		// Return the data provider
		return $aDataProvider;
	}

	/**
	 * This method generates an HTML anchor tag
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sHref
	 * @param string $sValue
	 * @param string [$sId]
	 * @param string [$sName]
	 * @param array [$aAttributes]
	 * @return string
	 **/
	public function getAnchor($sHref, $sValue, $sId = null, $sName = null, $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<a href=\"{$sHref}\" name=\"{$sName}\" id=\"{$sId}\" ";
		// Check for attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Finish off the tag
		$sHtml .= (string) ">{$sValue}</a>";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML button
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sName
	 * @param string $sLabel
	 * @param sting [$sId]
	 * @param array [$aAttributes]
	 * @return string
	 **/
	public function getButton($sName, $sLabel, $sId = null, $aAttributes = null) {

		// Start the element
		$sHtml = (string) "<button name=\"{$sName}\" ";
		// Check for an ID
		if (!empty($sId)) {
			$sHtml .= (string) "id=\"{$sId}\" ";
		}
		// Check for attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Finish off the tag
		$sHtml .= (string) ">{$sLabel}</button>";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML div with elements
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param array [$aElements]
	 * @param array [$aAttributes]
	 * @return string
	 **/
	public function getDiv($aElements = array(), $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<div ";
		// Check for attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the opening tag
		$sHtml .= (string) ">";
		// Check for elements
		if (!empty($aElements)) {
			// Load the elements
			$sHtml .= (string) $this->processElements($aElements);
		}
		// Finish the tag
		$sHtml .= (string) "</div>\n";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML drop down select form element
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sName
	 * @param string [$sId]
	 * @param array [$aDataProvider]
	 * @param array [$aAttributes]
	 * @param string [$sSelected]
	 * @return string
	 **/
	public function getDropdown($sName, $sId = null, $aDataProvider = array(), $aAttributes = array(), $sSelected = null) {
		// Start the HTML
		$sHtml = (string) "<select name=\"{$sName}\" ";
		// Check for an ID
		if (!empty($sId)) {
			$sHtml .= (string) "id=\"{$sId}\" ";
		}
		// Check for extra attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the opening tag
		$sHtml .= (string) ">";
		// Check to see if the Data Provider
		// is an array of objects or an
		// array of arrays
		if (!empty($aDataProvider)) {
			// Parse the data provider
			foreach ($aDataProvider as $sLabel => $sValue) {
				// Option placeholder
				$sOption = (string) "<option value=\"{$sValue}\" ";
				// Check to see if this value should be selected
				if ($sSelected == $sValue) {
					// Make this option selected
					$sOption .= (string) "selected=\"selected\" ";
				}
				// Finish the option tag
				$sOption .= (string) ">{$sLabel}</option>";
				// Append the option to the select
				$sHtml .= (string) $sOption;
			}
		}
		// Finish the dropdown
		$sHtml .= (string) "</select>";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML fieldset with elements
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param array [$aElements]
	 * @param array [$aAttributes]
	 * @return string
	 **/
	public function getFieldset($aElements = array(), $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<fieldset ";
		// Check for attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the opening tag
		$sHtml .= (string) ">";
		// Check for elements
		if (!empty($aElements)) {
			// Load the elements
			$sHtml .= (string) $this->processElements($aElements);
		}
		// Finish the tag
		$sHtml .= (string) "</fieldset>\n";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML form with elements
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @param string $sAction
	 * @param string $sMethod
	 * @param string $sName
	 * @param string [$sId]
	 * @param array [$aElements]
	 * @param array [$aAttributes]
	 * @return string
	 **/
	public function getForm($sAction, $sMethod, $sName, $sId = null, $aElements = array(), $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<form action=\"{$sAction}\" method=\"{$sMethod}\" name=\"{$sName}\" ";
		// Check for an ID
		if (!empty($sId)) {
			$sHtml .= (string) "id=\"{$sId}\" ";
		}
		// Check for attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the opening tag
		$sHtml .= (string) ">";
		// Check for elements
		if (!empty($aElements)) {
			// Load the elements
			$sHtml .= (string) $this->processElements($aElements);
		}
		// Finish the tag
		$sHtml .= (string) "</form>\n";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates and img tag
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sSource
	 * @param array [$aAttributes]
	 * @return string
	 **/
	public function getImage($sSource, $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<img src=\"{$sSource}\" ";
		// Check for attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Finish off the tag
		$sHtml .= (string) ">";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates a label tag
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @param string $sText
	 * @param string $sFor
	 * @param array [$aAttributes]
	 * @return string
	 */
	public function getLabel($sText, $sFor = null, $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<label ";
		// Check for a for attribute
		if (!empty($sFor)) {
			// Add the for attribute
			$sHtml .= (string) "fore=\"{$sFor}\" ";
		}
		// Check for attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Add the text and close the tag
		$sHtml .= (string) ">{$sText}</label>";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML input
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @throws Exception
	 * @param string $sType
	 * @param string $sName
	 * @param string [$sId]
	 * @param array [$aAttributes]
	 * @return string
	 **/
	public function getInput($sType, $sName, $sId = null, $aAttributes = array()) {
		// Allowed types
		$aAllowedTypes = array(
			self::INPUT_BUTTON, self::INPUT_CHECKBOX, self::INPUT_FILE,
			self::INPUT_HIDDEN, self::INPUT_IMAGE,    self::INPUT_PASSWORD,
			self::INPUT_RADIO,  self::INPUT_SUBMIT,   self::INPUT_TEXT
		);
		// Check to make sure the caller wants to generate a valid HTML input
		if (!in_array($sType, $aAllowedTypes)) {
			// Throw an exception
			throw new Exception("'{$sType}' is not a valid HTML input type.");
		}
		// Start the element
		$sHtml = (string) "<input type=\"{$sType}\" name=\"{$sName}\" ";
		// Check for an ID
		if (!empty($sId)) {
			// Add the ID
			$sHtml .= (string) "id=\"{$sId}\" ";
		}
		// Check for extra attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the tag
		$sHtml .= (string) ">";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates a link tag which are primarily used in the
	 * head of the page for loading in cascading stylesheets
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sRelative
	 * @param string $sType
	 * @param string $sHref
	 * @param array [$aAttributes]
	 * @return string
	 **/
	public function getLink($sRelative, $sType, $sHref, array $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<link rel=\"{$sRelative}\" type=\"{$sType}\" href=\"{$sHref}\" ";
		// Check for extra attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the tag
		$sHtml .= (string) ">\n";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML meta tag
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string [$sName]
	 * @param string [$sContent]
	 * @param array [$aAttributes]
	 * @return string
	 */
	public function getMetaTag($sName = null, $sContent = null, array $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<meta ";
		// Check for a name
		if (!empty($sName)) {
			// Set the name
			$sHtml .= (string) "name=\"{$sName}\" ";
		}
		// Check for content
		if (!empty($sContent)) {
			// Set the content
			$sHtml .= (string) "content=\"{$sContent}\" ";
		}
		// Check for extra attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the tag
		$sHtml .= (string) ">\n";
		// Return HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML script tag
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sType
	 * @param string [$sSrc]
	 * @param string [$sInline]
	 * @param array [$aAttributes]
	 * @return string
	 */
	public function getScript($sType, $sSrc = null, $sInline = null, $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<script type=\"{$sType}\" ";
		// Check to see if we have a source
		if (!empty($sSrc)) {
			$sHtml .= (string) "src=\"{$sSrc}\" ";
		}
		// Check for other attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the opening tag
		$sHtml .= (string) ">";
		// Check for inline code
		if (!empty($sInline)) {
			// Append the inline code
			$sHtml .= (string) $sInline;
		}
		// Finish the element
		$sHtml .= (string) "</script>\n";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML style tag
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sType
	 * @param string $sSource
	 * @param array [$aAttributes]
	 * @return string
	 */
	public function getStyle($sType, $sSource, $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<style type=\"{$sType}\" ";
		// Check for attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the opening tag
		$sHtml .= (string) ">";
		// Set the style source
		$sHtml .= (string) $sSource;
		// Finish the element
		$sHtml .= (string) "</style>\n";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates an HTML textarea
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sName
	 * @param string [$sId]
	 * @param string [$sContent]
	 * @param array [$aAttributes]
	 * @return string
	 **/
	public function getTextarea($sName, $sId = null, $sContent = null, $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<textarea name=\"{$sName}\" ";
		// Check for an ID
		if (!empty($sId)) {
			// Add the ID
			$sHtml .= (string) "id=\"{$sId}\" ";
		}
		// Check for extra attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the tag
		$sHtml .= (string) ">";
		// Check for a value
		if (!empty($sContent)) {
			// Add the value then
			$sHtml .= (string) $sContent;
		}
		// Close the tag
		$sHtml .= (string) "</textarea>";
		// Return the HTML
		return $sHtml;
	}

	/**
	 * This method generates a custom tag
	 * @package Framsie
	 * @subpackage FramsieHtml
	 * @access public
	 * @param string $sType
	 * @param boolean $bSelfClosing
	 * @param array $aElements
	 * @param array $aAttributes
	 * @return string
	 */
	public function getTag($sType, $bSelfClosing, $aElements = array(), $aAttributes = array()) {
		// Start the element
		$sHtml = (string) "<{$sType} ";
		// Check for attributes
		if (!empty($aAttributes)) {
			// Load the attributes
			$sHtml .= (string) $this->processAttributes($aAttributes);
		}
		// Close the opening tag
		$sHtml .= (string) ">";
		// Check for elements
		if (!empty($aElements)) {
			// Load the elements
			$sHtml .= (string) $this->processElements($aElements);
		}
		// Check to see if the tag closes itself
		if ($bSelfClosing === true) {
			// Finish the tag
			$sHtml .= (string) " />";
		} else {
			// Finish the tag
			$sHtml .= (string) "</{$sType}>\n";
		}
		// Return the HTML
		return $sHtml;
	}
}
