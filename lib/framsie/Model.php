<?php
/**
 * This class provides easy singletons for models
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieModel {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of this class
	 * @access protected
	 * @staticvar FramsieModel
	 */
	protected static $mInstance   = null;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method provides access to the singleton instance of this class
	 * @package Framsie
	 * @subpackage FramsieModel
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieModel self::$mInstance
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
	 * @subpackage FramsieModel
	 * @access public
	 * @static
	 * @param FramsieModel $oInstance
	 * @return FramsieModel self::$mInstance
	 */
	public static function setInstance(FramsieModel $oInstance) {
		// Set the external instance into the class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}
}
