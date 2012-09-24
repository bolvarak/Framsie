<?php
/**
 * This class provides an easy configuration file access
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieAssets {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of this class
	 * @access protected
	 * @staticvar FramsieAssets
	 */
	protected static $mInstance = null;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method provides access to the singleton instance of this class
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieAssets self::$mInstance
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
	 * This method sets an external instance into this class
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access public
	 * @static
	 * @param FramsieAssets $oInstance
	 * @return FramsieAssets self::$mInstance
	 */
	public static function setInstance(FramsieAssets $oInstance) {
		// Set the external instance into this class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply returns the instance of this class and is
	 * protected to enforce the singleton pattern
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access protected
	 * @return FramsieAssets $this
	 */
	protected function __constructor() {
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method simply loads the cache and caches the source if it isn't
	 * already cached or the the cache has expired, no minification is involved
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access protected
	 * @param string $sSource
	 * @param string $sName
	 * @return string
	 */
	protected function loadAsset($sSource, $sName) {
		// Check the cache for the source
		$sCachedSource = (string) FramsieCache::getInstance()->loadFromCache($sName);
		// Check for the source code
		if (empty($sCachedSource)) {
			// Cache the source
			FramsieCache::getInstance()->saveToCache($sName, $sSource);
			// Return the source
			return $sSource;
		}
		// Return the cached source
		return $sCachedSource;
	}

	/**
	 * This method minifies a javascript or CSS source set
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access protected
	 * @param string $sSource
	 * @return string
	 */
	protected function minifyAsset($sSource, $sName) {
		// Check the cache for the source
		$sCachedSource = (string) FramsieCache::getInstance()->loadFromCache($sName);
		// Check for the minified source cache
		if (empty($sCachedSource)) {
			// Minify the source
			$sMinifiedSource = (string) preg_replace('/(?|(\\s)+|(\\n)+|(\\r)+|(\\r\\n)+|(\\t))/', null, $sSource);
			// Cache the minified source
			FramsieCache::getInstance()->saveToCache($sName, $sMinifiedSource);
			// Return the source
			return $sMinifiedSource;
		}
		// Return the cache
		return $sCachedSource;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method minifies and returns either a single stylesheet or a
	 * batched set of stylesheets
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access public
	 * @param multitype $sStylesheet
	 * @param string $sName
	 * @param boolean $bMinify
	 * @return string
	 */
	public function getCss($sStylesheet, $sName, $bMinify = true) {
		// Create a CSS placeholder
		$sCss = (string) null;
		// Check to see if we are loading one stylesheet or batching them
		if (is_array($sStylesheet)) {
			// Loop through the stylesheets
			foreach ($sStylesheet as $sSheet) {
				// Append the stylesheet
				$sCss .= (string) Framsie::getInstance()->renderBlock(CSS_ASSETS_PATHDIRECTORY_SEPARATOR.$sSheet);
			}
		} else {
			// Set the stylesheet
			$sCss = (string) Framsie::getInstance()->renderBlock(CSS_ASSETS_PATH.DIRECTORY_SEPARATOR.$sStylesheet);
		}
		// Return the CSS
		return (($bMinify === true) ? $this->minifyAsset($sCss, $sName) : $this->loadAsset($sCss, $sName));
	}

	/**
	 * This method  returns an image
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access public
	 * @param string $sImage
	 * @param string $sName
	 * @return string
	 */
	public function getImage($sImage, $sName) {
		// Start the output buffer
		ob_start();
		// Load the image file
		readfile(BLOCK_PATH.DIRECTORY_SEPARATOR.IMG_ASSETS_PATH.DIRECTORY_SEPARATOR.$sImage);
		// Return the cached asset
		return ob_get_clean(); // $this->loadAsset($sImage, $sName);
	}

	/**
	 * This method returns either a single JS script or a batched set of scripts
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access public
	 * @param multitype $sJavascript
	 * @param string $sName
	 * @param boolean $bMinify
	 * @return string
	 */
	public function getJavascript($sJavascript, $sName, $bMinify = true) {
		// Create a javascript placeholder
		$sJs = (string) null;
		// Check to see if we are loading one script or batching them
		if (is_array($sJavascript)) { // Batching the scripts
			// Loop through the javascript
			foreach ($sJavascript as $sScript) {
				// Append the script
				$sJs .= (string) Framsie::getInstance()->renderBlock(JAVASCRIPT_ASSETS_PATH.DIRECTORY_SEPARATOR.$sScript);
			}
		} else {                      // Only one script
			// Set the JS
			$sJs = (string) Framsie::getInstance()->renderBlock(JAVASCRIPT_ASSETS_PATH.DIRECTORY_SEPARATOR.$sJavascript);
		}
		// Return the JS
		return (($bMinify === true) ? $this->minifyAsset($sJs, $sName) : $this->loadAsset($sJs, $sName));
	}
}
