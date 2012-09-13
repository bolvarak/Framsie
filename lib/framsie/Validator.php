<?php
/**
 * This class provides an easy validation application
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieValidator {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the pattern for alpha and basic punctuation validation
	 * @var string
	 */
	const ALPHA_BASIC_PUNC         = '/[a-zA-Z,\s]+/$';

	/**
	 * This constant contains the pattern for alphanumeric and basic punctuation validation
	 * @var string
	 */
	const ALPHANUMERIC_BASIC_PUNC  = '/[a-zA-Z0-9,\s]+/';

	/**
	 * This constant contains the pattern for alphanumeric validation
	 * @var string
	 */
	const ALPHANUMERIC             = '/[a-zA-Z0-9]+/';

	/**
	 * This constant contains the pattern for letters only validation
	 * @var string
	 */
	const ALPHA                    = '/[a-zA-Z]+/';

	/**
	 * This constant contains the pattern for email address validation
	 * @var string
	 */
	const EMAIL                    = '^((?:(?:(?:\w[\.\-\+]?)*)\w)+)\@((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$';

	/**
	 * This constant contains the pattern for integer and float validation
	 * @var string
	 */
	const INTEGER                  = '/-?[0-9]+\.[0-9]+/';

	/**
	 * This constant contains the pattern for positive whole number validation
	 * @var string
	 */
	const NUMERIC                  = '/[0-9]+/';


	const URI                      = '/http|https[a-zA-Z0-9]/';


	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////




	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////




	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
}