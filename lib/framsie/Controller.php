<?php
/**
 * This class provides the structure for controllers
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieController {
	
	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This constant contains the ecmascript script type constant
	 * @var string
	 */
	const SCRIPT_TYPE_ECMA = 'text/ecmascript';
	
	/**
	 * This constant contains the javascript script type constant
	 * @var string
	 */
	const SCRIPT_TYPE_JS   = 'text/javascript';
	
	/**
	 * This constant contains the vbscript script type constant
	 * @var string
	 */
	const SCRIPT_TYPE_VB   = 'text/vbscript';
	
	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This property contains the singleton instance of this controller
	 * @access protected
	 * @staticvar FramsieController
	 */
	protected static $mInstance = null;
	
	/**
	 * This property holds the current block file path to the view
	 * @access protected
	 * @var string
	 */
	protected $mBlockFile       = null;
	
	/**
	 * This tells the system whether or not to disable the layout
	 * @access protected
	 * @var boolean
	 */
	protected $mDisableLayout   = false;
	
	/**
	 * This property contains the page layout block name
	 * @access protected
	 * @var string
	 */
	protected $mLayout          = null;
	
	/**
	 * This property contains the page's meta tags
	 * @access protected
	 * @var array
	 */
	protected $mMetaTags        = array();
	
	/**
	 * This property contains the page title value
	 * @access protected
	 * @var string
	 */
	protected $mPageTitle       = null;
	
	/**
	 * This property contains the page variables for the view
	 * @access protected
	 * @var array
	 */
	protected $mPageValues      = array();
	
	/**
	 * This property contains the FramsieRequestObject 
	 * associated with this controller
	 * @access protected
	 * @var FramsieRequestObject
	 */
	protected $mRequest         = null;
	
	/**
	 * This property contains the page's scripts to load
	 * @var array
	 */
	protected $mScripts         = array();
	
	/**
	 * This property contains the page's styleshets to load
	 * @var array
	 */
	protected $mStylesheets     = array();
	
	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method provides access to the singleton instance of this class
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @static
	 * @param boolean [$bReset]
	 * @return FramsieController self::$mInstance
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
	 * This method sets an external instance into the class, this is generally
	 * only ever used in testing, primarily with phpUnit
	 * @param FramsieController $oInstance
	 * @return FramsieController self::$mInstance
	 */
	public static function setInstance(FramsieController $oInstance) {
		// Set the external instance into the class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * The constructor simply returns the instance
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return FramsieController $this
	 */
	public function __construct() {
		// Return the instance
		return $this;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method adds a meta tag for the current page to the instance of 
	 * the current controller
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sName
	 * @param string $sContent
	 * @param string [$sHttpEquiv]
	 * @param string [$sScheme]
	 * @return FramsieController $this
	 */
	public function addMetaTag($sName, $sContent, $sHttpEquiv = null, $sScheme = null) {
		// Add the meta tag to the instance
		array_push($this->mMetaTags, array(
			'sContent'   => (string) $sContent, 
			'sHttpEquiv' => (string) $sHttpEquiv, 
			'sName'      => (string) $sName, 
			'sScheme'    => (string) $sScheme
		));
		// Return the instance
		return $this;
	}
	
	/**
	 * This method adds a script to the page into the instance of the current
	 * controller class
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sSource
	 * @param string $sType
	 * @param boolean [$bSourceIsLink]
	 * @return FramsieController $this
	 */
	public function addScript($sSource, $sType = self::SCRIPT_TYPE_JS, $bSourceIsLink = true) {
		// Create the script object
		$oScript = new stdClass();
		// Set the source
		$oScript->sSource       = (string) $sSource;
		// Set the source type
		$oScript->bSourceIsLink = (boolean) $bSourceIsLink;
		// Set the script type
		$oScript->sType         = (string) $sType;
		// Add the script to the instance
		array_push($this->mScripts, $oScript);
		// Return the instance
		return $this;
	}
	
	/**
	 * This method adds a stylesheet into the instance of the current controller
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sSource
	 * @param boolean [$bSourceIsLink]
	 * @return FramsieController
	 */
	public function addStylesheet($sSource, $bSourceIsLink = true) {
		// Create the stylesheet object
		$oStylesheet = new stdClass();
		// Set the source
		$oStylesheet->sSource       = (string) $sSource;
		// Set the source type
		$oStylesheet->bSourceIsLink = (boolean) $bSourceIsLink;
		// Add the stylesheet to the instance
		array_push($this->mStylesheets, $oStylesheet);
		// Return the instance
		return $this;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method returns the currently set block file for the associated view
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return string
	 */
	public function getBlockFile() {
		// Return the current block file into the system
		return $this->mBlockFile;
	}
	
	/**
	 * This method returns the current layout active status
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return boolean
	 */
	public function getDisableLayout() {
		// Return the current layout status
		return $this->mDisableLayout;
	}
	
	/**
	 * This method returns the Framsie parent instance
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return Framsie
	 */
	public function getHelper() {
		// Return the latest instance of Framsie
		return Framsie::getInstance();
	}
	
	/**
	 * This method returns the current layout block
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return string
	 */
	public function getLayout() {
		// Return the current layout block
		return $this->mLayout;
	}
	
	/**
	 * This method returns the currently set page title in the instance
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return string
	 */
	public function getPageTitle() {
		// Return the page title currently set in the instance
		return $this->mPageTitle;
	}
	
	/**
	 * This method returns a set page variable if it exists
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @throws Exception
	 * @param string $sName
	 * @return multitype
	 */
	public function getPageValue($sName) {
		// Make sure the page value exists
		if (empty($this->mPageValues[$sName])) {
			// Throw an exception as this works just like a standard variable
			throw new Exception("No page value with the name of \"{$sName}\" has been set.");
		}
		// Return the page value
		return $this->mPageValues[$sName];
	}
	
	/**
	 * This method returns the request object associated with this controller
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return FramsieRequestObject $this->mRequest
	 */
	public function getRequest() {
		// Return the request object
		return $this->mRequest;
	}
	
	/**
	 * This method returns the scripts that are stored in this controller
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param boolean $bAsHtml
	 * @return multitype
	 */
	public function getScripts($bAsHtml = true) {
		// Check to see if we need to generate the HTML
		if ($bAsHtml === true) {
			// Create a scripts placeholder
			$sScripts = (string) null;
			// Loop through the scripts
			foreach ($this->mScripts as $oScript) {
				// Check for a source link
				if ($oScript->bSourceIsLink === true) {
					// Generate the script tag
					$sScripts .= (string) FramsieHtml::getInstance()->getScript($oScript->sType, $oScript->sSource);
				} else {
					// Generate the script tag
					$sScripts .= (string) FramsieHtml::getInstance()->getScript($oScript->sType, null, $oScript->sSource);
				}
			}
			// Return the scripts
			return $sScripts;
		}
		// Return the scripts
		return $this->mScripts;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method sets the block file into the current instance
	 * @package Framsie
	 * @subpackage FramsieController
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
	 * This method tells the system whether or not to disable the layout view
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param boolean $bDisable
	 * @return FramsieController $this
	 */
	public function setDisableLayout($bDisable = true) {
		// Tell the system whether or not to disable the layout
		$this->mDisableLayout = (boolean) $bDisable;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the layout block into the instance
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sBlock
	 * @return FramsieController $this
	 */
	public function setLayout($sBlock) {
		// Set the layout block into the system
		$this->mLayout = (string) $sBlock;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the page title into the instance
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sTitle
	 * @return FramsieController $this
	 */
	public function setPageTitle($sTitle) {
		// Set the page title into the instance
		$this->mPageTitle = (string) $sTitle;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method adds a page variable into the instance
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sName
	 * @param multitype $sValue
	 * @return FramsieController $this
	 */
	public function setPageValue($sName, $sValue) {
		// Set the page value into the system
		$this->mPageValues[$sName] = $sValue;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method is mainly for the request object to set itself into 
	 * the controller instance
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param FramsieRequestObject $oRequest
	 * @return FramsieController $this
	 */
	public function setRequest(FramsieRequestObject $oRequest) {
		// Set the request object into the instance
		$this->mRequest = $oRequest;
		// Return the instance
		return $this;
	}
}
