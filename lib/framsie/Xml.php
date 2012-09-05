<?php
/**
 * This class provides easy XML encoding and decoding
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieXml {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the value for UTF-8 encodings
	 * @var string
	 */
	const XML_ENCODING_UTF8       = 'UTF-8';

	/**
	 * This constant contains the value for double space indentation in XML strings
	 * @var string
	 */
	const XML_INDENT_DOUBLE_SPACE = '\s\s';

	/**
	 * This constant contains the value for space indentation in XML strings
	 * @var string
	 */
	const XML_INDENT_SPACE        = '\s';

	/**
	 * This constant contains the value for tab indentation in XML strings
	 * @var string
	 */
	const XML_INDENT_TAB          = '\t';

	/**
	 * This constant contains the value for XML version 1.0
	 * @var string
	 */
	const XML_VERSION_1_0         = '1.0';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of this class
	 * @access protected
	 * @staticvar FramsieXml
	 */
	protected static $mInstance   = null;

	/**
	 * This property contains the instance of the XML Reader
	 * @access protected
	 * @var XMLReader
	 */
	protected $mReader            = null;

	/**
	 * This property contains the instance of the XML Writer
	 * @access protected
	 * @var XMLWriter
	 */
	protected $mWriter            = null;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method provides access to the singleton instance of this class
	 * @package Framsie
	 * @subpackage FramsieXml
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieXml self::$mInstance
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
	 * @subpackage FramsieXml
	 * @access public
	 * @static
	 * @param FramsieXml $oInstance
	 * @return FramsieXml self::$mInstance
	 */
	public static function setInstance(FramsieXml $oInstance) {
		// Set the external instance into the class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply returns the instance and is protected to
	 * enforce the singleton pattern throughout the system
	 * @package Framsie
	 * @subpackage FramsieXml
	 * @access protected
	 * @return FramsieXml $this
	 */
	protected function __constructor() {
		// Create the reader
		$this->mReader = new XMLReader();
		// Create ther XML writer
		$this->mWriter = new XMLWriter();
		// Open memory for the writer
		$this->mWriter->openMemory();
		// Turn indentation on
		$this->mWriter->setIndent(true);
		// Set the indentation string
		$this->mWriter->setIndentString(self::XML_INDENT_TAB);
		// Start the document
		$this->mWriter->startDocument(self::XML_VERSION_1_0, self::XML_ENCODING_UTF8);
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////




	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////


	public function toArray($sXml) {

	}


	public function toObject($sXml) {

	}


	public function toXml($mEntity) {

	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////




	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////



}
