<?php

class FramsieInterpreter {

	////////////////////////////////////////////////////////////////////////////
	/// Constants /////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the Framsie close tag notator
	 * @var string
	 */
	const CLOSE_NOTATOR_FRAMSIE              = '\s+::}';


	const CLOSE_NOTATOR_PERL_HTML_TEMPLATE   = '\<\/tmp(if|loop|unless|var|while)\>';

	/**
	 * This constant contains the Smarty close tag notator
	 * @var string
	 */
	const CLOSE_NOTATOR_SMARTY               = '\}';

	/**
	 * This constant contains the notation definition specific to Framsie
	 * @var integer
	 */
	const NOTATOR_FRAMSIE                    = 0;

	/**
	 * This constant contains the notation definition specific to Perl's HTML::Template
	 * @var unknown_type
	 */
	const NOTATOR_PERL_HTML_TEMPLATE         = 1;

	/**
	 * This constant contains the notation definition specific to Smarty
	 * @var integer
	 */
	const NOTATOR_SMARTY                     = 2;

	/**
	 * This constant contains the Framsie notator open tag
	 * @var string
	 */
	const OPEN_NOTATOR_FRAMSIE               = '{::\s+';

	/**
	 * This constant contains the Perl HTML::Tempalate notator open tag
	 * @var string
	 */
	const OPEN_NOTATOR_PERL_HTML             = '\<tmpl_(if|loop|unless|var|while)\s*?\>';

	/**
	 * This contains the Smart notator open tag
	 * @var string
	 */
	const OPEN_NOTATOR_SMARTY                = '{';


	////////////////////////////////////////////////////////////////////////////
	/// Properties ////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the compiled source
	 * @access protected
	 * @var string
	 */
	protected $mCompiled                     = null;

	/**
	 * This property contains the variable container for the template file
	 * @access protected
	 * @var array|object
	 */
	protected $mContainer                    = null;

	/**
	 * This propery tells the system whether or not the variable container is an object or not
	 * @access protected
	 * @var boolean
	 */
	protected $mContainerIsObject            = true;

	/**
	 * This property contains the current line in process
	 * @access protected
	 * @var string
	 */
	protected $mLineInProcessing             = null;

	/**
	 * This property contains the interpreter notation definition
	 * @access protected
	 * @var integer
	 */
	protected $mNotation                     = self::NOTATOR_FRAMSIE;

	/**
	 * This property contains the tempalte source
	 * @access protected
	 * @var string
	 */
	protected $mSource                       = null;

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor sets up the container object and interpreter notation, if present
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access public
	 * @param FramsieInterpreter|object $oContainer
	 * @param integer $iNotator
	 * @return FramsieInterpreter $this
	 */
	public function __construct($mContainer = self, $iNotator = self::NOTATOR_FRAMSIE) {
		// Set the container
		$this->mContainer = $mContainer;
		// Check to container for a variable type
		if (is_array($mContainer)) {
			// The container is an array
			$this->mContainerIsObject = false;
		} else {
			// The container is an object
			$this->mContainerIsObject = true;
		}
		// Set the interpreter notation
		$this->mNotation  = $iNotator;
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method checks the source/line for a Framsie notator
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access protected
	 * @param string $sSource
	 * @return boolean
	 */
	protected function checkForFramsieNotator($sSource) {
		// Check for the notator
		if (preg_match('%'.self::OPEN_NOTATOR_FRAMSIE.'%i', $sSource)) {
			// A match was found, we're done
			return true;
		}
		// No match was found, we're done
		return false;
	}

	/**
	 * This method checks for a notator in the source/line
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access protected
	 * @param string $sSource
	 * @return boolean
	 */
	protected function checkForNotation($sSource) {
		// Determine the notator
		switch ($this->mNotation) {
			// Framsie
			case self::NOTATOR_FRAMSIE            : return $this->checkForFramsieNotator($sSource);          break;
			// Perl HTML::Template
			case self::NOTATOR_PERL_HTML_TEMPLATE : return $this->checkForPerlHtmlTemplateNotator($sSource); break;
			// Smarty
			case self::NOTATOR_SMARTY             : return $this->checkForSmartyNotator($sSource);           break;
			// Default
			default                               : return false;                                            break;
		}
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////




	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////




	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the variable container
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access public
	 * @return array|object
	 */
	public function getContainer() {
		// Return the container
		return $this->mContainer;
	}

	/**
	 * This method returns the container object status
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access public
	 * @return boolean
	 */
	public function getContainerIsObject() {
		// Return the container object status
		return $this->mContainerIsObject;
	}

	/**
	 * This method returns the interpreter notation type
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access public
	 * @return integer
	 */
	public function getNotation() {
		// Return the interpreter notation
		return $this->mNotation;
	}

	/**
	 * This method returns the current template source
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access public
	 * @return string
	 */
	public function getSource() {
		// Return the template source
		return $this->mSource;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the container and container type into the system
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access public
	 * @param array|object $mContainer
	 * @return FramsieInterpreter $this
	 */
	public function setContainer($mContainer) {
		// Set the container into the system
		$this->mContainer = $mContainer;
		// Check to container type
		if (is_array($mContainer)) {
			// Set the container type into the system
			$this->mContainerIsObject = false;
		} else {
			// Set the container type into the system
			$this->mContainerIsObject = true;
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the interpreter notator into the system
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access public
	 * @param integer $iNotator
	 * @return FramsieInterpreter $this
	 */
	public function setNotation($iNotator) {
		// Set the notator into the system
		$this->mNotation = (integer) $iNotator;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the template source into the system
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access public
	 * @param string $sSource
	 * @return FramsieInterpreter $this
	 */
	public function setSource($sSource) {
		// Set the source into the system
		$this->mSource = (string) $sSource;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the source from a file
	 * @package Framsie
	 * @subpackage FramsieInterpreter
	 * @access public
	 * @param string $sFileName
	 * @return FramsieInterpreter $this
	 */
	public function setSourceFile($sFileName) {
		// Make sure the file exists
		if (file_exists($sFileName) === false) {
			// Throw an exception
			FramsieError::Trigger('FRAMTFN', array($sFileName));
		}
		// Set the source filename
		$this->mSource = fopen($sFileName, 'r');
		// Return the instance
		return $this;
	}
}
