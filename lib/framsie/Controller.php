<?php
/**
 * This class provides the structure for controllers
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
abstract class FramsieController {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the font header content-tyle
	 * @var string
	 */
	const FONT              = 'application/font';

	/**
	 * This constant contains the EOT font header content-type
	 * @var string
	 */
	const FONT_EOT          = 'application/vnd.ms-fontobject';

	/**
	 * This constant contains the TTF font header content-type
	 * @var string
	 */
	const FONT_TTF          = 'application/octet-stream';

	/**
	 * This constant contains the WOFF font header content-type
	 * @var string
	 */
	const FONT_WOFF         = 'application/font-woff';

	/**
	 * This constant contains the CSS header content-type
	 * @var string
	 */
	const HEADER_CSS        = 'text/css';

	/**
	 * This constant contains the HTML header content-type
	 * @var string
	 */
	const HEADER_HTML       = 'text/html';

	/**
	 * This constant contains the Javascript header content-type
	 * @var string
	 */
	const HEADER_JAVASCRIPT = 'text/javascript';

	/**
	 * This constant contains the JSON header content-type
	 * @var string
	 */
	const HEADER_JSON       = 'application/json';

	/**
	 * This constant contains the TXT header content-type
	 * @var string
	 */
	const HEADER_TEXT       = 'text/plain';

	/**
	 * This constant contains the XML header content-type
	 * @var string
	 */
	const HEADER_XML        = 'text/xml';

	/**
	 * This constant contains the gif header content-type
	 * @var string
	 */
	const IMG_GIF           = 'image/gif';

	/**
	 * This constant contains the jpeg header content-type
	 * @var string
	 */
	const IMG_JPEG          = 'image/jpeg';

	/**
	 * This constant contains the jpg header content-type
	 * @var string
	 */
	const IMG_JPG           = 'image/jpg';

	/**
	 * This constant contains the png header content-type
	 * @var string
	 */
	const IMG_PNG           = 'image/png';

	/**
	 * This constant contains the svg header content-type
	 * @var string
	 */
	const IMG_SVG           = 'image/svg+xml';

	/**
	 * This constant contains the tif header content-type
	 * @var string
	 */
	const IMG_TIF           = 'image/tiff';

	/**
	 * This constant contains the ecmascript script type constant
	 * @var string
	 */
	const SCRIPT_TYPE_ECMA  = 'text/ecmascript';

	/**
	 * This constant contains the javascript script type constant
	 * @var string
	 */
	const SCRIPT_TYPE_JS    = 'text/javascript';

	/**
	 * This constant contains the vbscript script type constant
	 * @var string
	 */
	const SCRIPT_TYPE_VB    = 'text/vbscript';

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the class(es) to append to the body element
	 * @access protected
	 * @var string
	 */
	protected $mBodyClass       = null;

	/**
	 * This property contains the body element onload subroutine
	 * @access protected
	 * @var string
	 */
	protected $mBodyOnload      = null;

	/**
	 * This property tells the system whether or not to disable the layout rendering
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
	 * @var stdClass
	 */
	protected $mPageValues      = null;

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

	/**
	 * This property contains the view object
	 * @access protected
	 * @var FramsieView
	 */
	protected $mView            = null;

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply returns the instance
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sLayout
	 * @return FramsieController $this
	 */
	public function __construct($sLayout = null) {
		// Check to see if a layout has been specified
		if (empty($sLayout) === false) {
			// Set the layout
			$this->mLayout = (string) $sLayout;
		}
		// Reset the page values
		$this->mPageValues = new stdClass();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method redirects the current request
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access protected
	 * @param string $sUrl
	 * @return void
	 */
	protected function redirectRequest($sUrl) {
		// Check to see if there should be a new controller
		if (substr($sUrl, 0, 1) === '/') {
			// We will redirect the a new controller
			header("Location:  {$sUrl}");
		} else {
			// Set the controller name
			$sController = (string) strtolower(str_replace('Controller', null, __CLASS__));
			// Redirect to the view
			header("Location:  {$sController}/{$sUrl}");
		}
	}

	/**
	 * This method encodes and sends an HTTP Query String response for AJAX method endpoints
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access protected
	 * @param multitype $mResponse
	 * @return void
	 */
	protected function sendHttpQueryEndpointResponse($mResponse) {
		// Encode and send the response
		die(http_build_query($mResponse));
	}

	/**
	 * This method encodes and sends a JSON response for AJAX method endpoints
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access protected
	 * @param multitype $mResponse
	 * @return void
	 */
	protected function sendJsonEndpointResponse($mResponse) {
		// Encode and send the response
		die(json_encode($mResponse));
	}

	/**
	 * This method encodes and sends an XML response for AJAX method endpoints
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access protected
	 * @param multitype $mResponse
	 * @return void
	 */
	protected function sendXmlEndpointResponse($mResponse) {
		// Encode and send the response
		// die(FramsieXml::getInstance()->Encode($mResponse));
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
		// Initialize the meta tag
		$oMetaTag = new stdClass();
		// Set the content
		$oMetaTag->sContent   = (string) $sContent;
		// Set the http-equiv if any
		$oMetaTag->sHttpEquiv = (string) $sHttpEquiv;
		// Set the name
		$oMetaTag->sName      = (string) $sName;
		// Set the content
		$oMetaTag->sScheme    = (string) $sScheme;
		// Add the meta tag to the instance
		array_push($this->mMetaTags, $oMetaTag);
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
	 * @param string [$sMedia]
	 * @return FramsieController
	 */
	public function addStylesheet($sSource, $bSourceIsLink = true, $sMedia = null) {
		// Create the stylesheet object
		$oStylesheet = new stdClass();
		// Set the source
		$oStylesheet->sSource       = (string) $sSource;
		// Set the source type
		$oStylesheet->bSourceIsLink = (boolean) $bSourceIsLink;
		// Set the media
		$oStylesheet->sMedia        = (string) $sMedia;
		// Add the stylesheet to the instance
		array_push($this->mStylesheets, $oStylesheet);
		// Return the instance
		return $this;
	}

	/**
	 * This method renders the layout set into the controller or the default layout
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return FramsieController $this
	 */
	public function renderLayout() {
		// Make sure a layout should be rendered
		if ($this->mDisableLayout === false) {
			// Set the filename
			$sFilename = (string) (empty($this->mLayout) ? 'templates'.DIRECTORY_SEPARATOR.'layout.phtml' : $this->mLayout);
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
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the body element class styles
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return string
	 */
	public function getBodyClass() {
		// Return the body class
		return $this->mBodyClass;
	}

	/**
	 * This method returns the body element onload subroutine
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return string
	 */
	public function getBodyOnload() {
		// Return the body onload
		return $this->mBodyOnload;
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
	 * This method generates an image URL for the system
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sImage
	 * @return string
	 */
	public function getImageUrl($sImage) {
		// Return the URL
		return $this->getUrl('assets', 'image', 'file', base64_encode($sImage));
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
	 * This method returns the meta tags stored in this controller
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param boolean $bAsHtml
	 * @return multitype
	 */
	public function getMetaTags($bAsHtml = true) {
		// Check to see if we neet to generate the HTML
		if ($bAsHtml === true) {
			// Create a styles placeholder
			$sMetaTags = (string) null;
			// Loop through the meta tags
			foreach ($this->mMetaTags as $oMetaTag) {
				// Check for an htt-equiv
				if (!empty($oMetaTag->sHttpEquiv)) {
					// Generate the meta tag
					$sMetaTags .= (string) FramsieHtml::getInstance()->getMetaTag(null, $oMetaTag->sContent, array(
						'http-equiv' => (string) $oMetaTag->sHttpEquiv
					));
				} else {
					// Generate the meta tag
					$sMetaTags .- (string) FramsieHtml::getInstance()->getMetaTag($oMetaTag->sName, $oMetaTag->sContent);
				}
			}
			// Return the meta tags
			return $sMetaTags;
		}
		// Return the meta tags
		return $this->mMetaTags;
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

	/**
	 * This method returns the URL for a script
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sScript
	 * @return string
	 */
	public function getScriptUrl($sScript) {
		// Return the script URL
		return $this->getUrl('assets', 'script', 'file', base64_encode($sScript));
	}

	/**
	 * This method returns the styles that are stored in this controller
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param boolean $bAsHtml
	 * @return multitype
	 */
	public function getStyles($bAsHtml = true) {
		// Check to see if we neet to generate the HTML
		if ($bAsHtml === true) {
			// Create a styles placeholder
			$sStyles = (string) null;
			// Loop through the styles
			foreach ($this->mStylesheets as $oStylesheet) {
				// Check for a source link
				if ($oStylesheet->bSourceIsLink === true) {
					// Generate the link tag
					$sStyles .= (string) FramsieHtml::getInstance()->getLink('stylesheet', 'text/css', $oStylesheet->sSource, (empty($oStylesheet->sMedia) ? array() : array('media' => $oStylesheet->sMedia)));
				} else {
					// Generate the style tag
					$sStyles .= (string) FramsieHtml::getInstance()->getStyle('text/css', $oStylesheet->sSource);
				}
			}
			// Return the stylesheets
			return $sStyles;
		}
		// Return the styles
		return $this->mStylesheets;
	}

	/**
	 * This method returns a style URL
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sStyle
	 * @param string $sVersion
	 * @return string
	 */
	public function getStyleUrl($sStyle, $sVersion = null) {
		// Generate the URL
		$sUrl = (string) $this->getUrl('assets', 'style', 'file', base64_encode($sStyle));
		// Return the URL
		return (empty($sVersion) ? $sUrl : "{$sUrl}?v={$sVersion}");
	}

	/**
	 * This method builds a URL and returns it or returns the current URL
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string controller
	 * @param string view
	 * @param string query string
	 * @return string
	 */
	public function getUrl() {
		// Check for arguments
		if (func_num_args() > 0) {
			// URL placeholder
			$sUrl = (string) '/';
			// Loop through the arguments
			foreach (func_get_args() as $sArgument) {
				// Append to the URL
				$sUrl .= (string) str_replace('/', '%2f', urlencode($sArgument)).'/';
			}
			// Return the URL
			return $sUrl;
		}
		// Return the REQUEST_URI
		return $_SERVER['REQUEST_URI'];
	}

	/**
	 * This method returns the current view object set into the system
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @return FramsieView
	 */
	public function getView() {
		// Return the current view object
		return $this->mView;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the body element class styles
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sClass
	 * @return FramsieController $this
	 */
	public function setBodyClass($sClass) {
		// Set the body element class
		$this->mBodyClass = (string) $sClass;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the body element onload subroutine
	 * @package Framse
	 * @subpackage FramsieController
	 * @access public
	 * @param string $sRoutine
	 * @return FramsieController $this
	 */
	public function setBodyOnload($sRoutine) {
		// Set the body element onload subroutine
		$this->mBodyClass = (string) $sRoutine;
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
	 * This method turns the current controller into a
	 * HTML RPC endpoint for AJAX or REST services
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param boolean $bDisableView
	 * @param boolean $bDisableLayout
	 * @return FramsieController $this
	 */
	public function setEndpointHtml($bDisableView = false, $bDisableLayout = true) {
		// Check to see if we need to disable the view
		if ($bDisableView === true) {
			// Disable the view
			$this->getView()->setDisableView();
		}
		// Check to see if we need to disable the layout
		if ($bDisableLayout === true) {
			// Disable the layout
			$this->setDisableLayout();
		}
		// Set the header
		$this->setHeaderContentType(self::HEADER_HTML);
		// Return the instance
		return $this;
	}

	/**
	 * This method turns the current controller into a
	 * JSON RPC endpoint for AJAX or REST services
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param boolean $bDisableView
	 * @return FramsieController $this
	 */
	public function setEndpointJson($bDisableView = false) {
		// Check to see if we need to disable the view
		if ($bDisableView === true) {
			// Disable the view
			$this->getView()->setDisableView();
		}
		// Disable the layout
		$this->setDisableLayout();
		// Set the header
		$this->setHeaderContentType(self::HEADER_JSON);
		// Return the instance
		return $this;
	}

	/**
	 * This method turns the current controller into a
	 * TXT RPC endpoint for AJAX or REST services
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param boolean $bDisableView
	 * @return FramsieController $this
	 */
	public function setEndpointText($bDisableView = false) {
		// Check to see if we need to disable the view
		if ($bDisableView === true) {
			// Disable the view
			$this->getView()->setDisableView();
		}
		// Disable the layout
		$this->setDisableLayout();
		// Set the header
		$this->setHeaderContentType(self::HEADER_TEXT);
		// Return the instance
		return $this;
	}

	/**
	 * This method turns the current controller into an
	 * XML RPC endpoint for AJAX or REST services
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param boolean $bDisableView
	 * @return FramsieController $this
	 */
	public function setEndpointXml($bDisableView = false) {
		// Check to see if we need to disable the view
		if ($bDisableView === true) {
			// Disable the view
			$this->getView()->setDisableView();
		}
		// Disable the layout
		$this->setDisableLayout();
		// Set the header
		$this->setHeaderContentType(self::HEADER_XML);
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the header content type for when we are not simply
	 * displaying pretty HTML
	 * @param string $sContentType
	 * @return FramsieController $this
	 */
	public function setHeaderContentType($sContentType = self::HEADER_HTML) {
		// Set the header content-type
		header("Content-Type:  {$sContentType}");
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

	/**
	 * This method sets a view ovbject into the controller
	 * @package Framsie
	 * @subpackage FramsieController
	 * @access public
	 * @param FramsieView $oView
	 * @return FramsieController $this
	 */
	public function setView(FramsieView $oView) {
		// Set the view object into the system
		$this->mView = $oView;
		// Return the instance
		return $this;
	}
}
