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
	protected $mBlockFile   = null;

	/**
	 * This property tells the system whether or not to disable the view rendering
	 * @access protected
	 * @var boolean
	 */
	protected $mDisableView = false;

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
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method renders the block file associated with the view object
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @return string
	 */
	public function renderView() {
		// Set the filename
		$sFilename = (string) $this->mBlockFile;
		// Check for an extension
		if (!preg_match('/\.css|js|php|phtml$/i', $sFilename)) {
			// Append the file extension to the filename
			$sFilename .= (string) "{$sFilename}.phtml";
		}
		// Make sure the file exists
		if (!file_exists(BLOCK_PATH.DIRECTORY_SEPARATOR.$sFilename)) {
			// Throw an exception because if this method is called, obviously
			// the block is needed to continue
			throw new Exception("The block file \"{$sFilename}\" does not exist as it was called, nor does it exist in the blocks directory");
		}
		// Start the capture of the output buffer stream
		ob_start();
		// Load the block
		require_once(BLOCK_PATH.DIRECTORY_SEPARATOR.$sFilename);
		// Depending on the print notification either return the buffer
		// or simply print the buffer directly to the screen
		return ob_get_clean();
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

	/**
	 * This is just a helper method to render the block
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @return string
	 */
	public function getContent() {
		// Return the rendered block
		return $this->renderView();
	}

	/**
	 * This is just a helper method that returns the current controller
	 * instance from the Framsie parent class
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @return FramsieController
	 */
	public function getController() {
		// Return the current controller from Framsie
		return Framsie::getInstance()->getController();
	}

	/**
	 * This method returns the current view active status
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @return boolean
	 */
	public function getDisableView() {
		// Return the current view status
		return $this->mDisableView;
	}

	/**
	 * This method returns a set page variable if it exists
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @throws Exception
	 * @param string $sName
	 * @return multitype
	 */
	public function getPageValue($sName) {
		// Make sure the page value exists
		if (!property_exists($this, $sName)) {
			// Throw an exception as this works just like a standard variable
			throw new Exception("No page value with the name of \"{$sName}\" has been set.");
		}
		// Return the page value
		return $this->{$sName};
	}

	/**
	 * This is just a helper method that returns the current request
	 * object instance from the Framsie parent class
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @return FramsieRequestObject
	 */
	public function getRequest() {
		// Return the current request object from Framsie
		return Framsie::getInstance()->getRequest();
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

	/**
	 * This method tells the system whether or not to disable the view
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @param boolean $bDisable
	 * @return FramsieView $this
	 */
	public function setDisableView($bDisable = true) {
		// Tell the system wheter or not to disable the view
		$this->mDisableView = (boolean) $bDisable;
		// Return the instance
		return $this;
	}

	/**
	 * This method adds a page variable into the instance
	 * @package Framsie
	 * @subpackage FramsieView
	 * @access public
	 * @param string $sName
	 * @param multitype $sValue
	 * @return FramsieView $this
	 */
	public function setPageValue($sName, $sValue) {
		// Set the page value into the system
		$this->{$sName} = $sValue;
		// Return the instance
		return $this;
	}
}
