<?php
/**
 * This class provides the structure for providing error reporting
 * @package Framsie
 * @subpackage ErrorController
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class ErrorController extends FramsieController {

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The contructor sets the layout into the parent
	 * @package Framsie
	 * @subpackage ErrorController
	 * @access public
	 * @return ErrorController
	 */
	public function __construct() {
		// Set the layout in the parent constructor
		parent::__construct('templates'.DIRECTORY_SEPARATOR.'layout.phtml');
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// View Methods /////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This view action simply displays the error
	 * @package Framsie
	 * @subpackage ErrorController
	 * @access public
	 * @return void
	 */
	public function defaultView() {
		// Set the page title
		$this->setPageTitle('Error!');
	}
}
