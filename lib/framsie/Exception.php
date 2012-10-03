<?php

class FramsieException extends Exception {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////


	const BLOCK_FILE_NOT_FOUND = 2;


	const CONTROLLER_NOT_FOUND = 0;


	const CSS_ASSET_NOT_FOUND  = 4;


	const JS_ASSET_NOT_FOUND   = 5;


	const SERVER_ERROR         = 500;


	const VIEW_NOT_FOUND       = 1;

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the exception code
	 * @access protected
	 * @var integer
	 */
	protected $mFramsieCode = null;

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor sets the message and code into the parent as well as
	 * sets the Framsie specific code into the instance
	 * @package Framsie
	 * @subpackage FramsieException
	 * @access public
	 * @param string $sMessage
	 * @param integer $iFramsieCode
	 * @return FramsieException
	 */
	public function __construct($sMessage, $iFramsieCode) {
		// Execute the parent constructor
		parent::__construct($sMessage, (integer) bin2hex($iFramsieCode));
		// Set the Framsie Exception Code
		$this->mFramsieCode = (integer) $iFramsieCode;
		// Return the instance
		return $this;
	}

}