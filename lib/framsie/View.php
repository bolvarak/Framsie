<?php
/**
 * This class provides the structure for views
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieView {
	
	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This property contains the block file that needs to be loaded
	 * @access protected
	 * @var string
	 */
	protected $mBlockFile = null;
	
	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * The constructor simply returns an instance of the class
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @return FramsieView $this
	 */
	public function __construct() {
		// Return the instance
		return $this;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method returns the currently set block file for the associated view
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @return string
	 */
	public function getBlockFile() {
		// Return the current block file into the system
		return $this->mBlockFile;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method sets the block file into the current instance
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @param string $sFilename
	 * @return FramsieView $this
	 */
	public function setBlockFile($sFilename) {
		// Set the block file into the system
		$this->mBlockFile = (string) $sFilename;
		// Return the instance
		return $this;
	}
}
