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
	 * This method minifies a javascript or CSS source set
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @uses JSMin
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
			$sMinifiedSource = (string) JSMin::minify($sSource);
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
	 * @return string
	 */
	public function getCss($sStylesheet, $sName) {
		// Create a CSS placeholder
		$sCss = (string) null;
		// Check to see if we are loading one stylesheet or batching them
		if (is_array($sStylesheet)) {
			// Loop through the stylesheets
			foreach ($sStylesheet as $sSheet) {
				// Append the stylesheet
				$sCss .= (string) Framsie::getInstance()->renderBlock($sStylesheet);
			}
		} else {
			// Set the stylesheet
			$sCss = (string) Framsie::getInstance()->renderBlock($sStylesheet);
		}
		// Return the CSS
		return $this->minifyAsset($sCss, $sName);
	}
	
	/**
	 * This method returns either a single JS script or a batched set of scripts
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access public
	 * @param multitype $sJavascript
	 * @param string $sName
	 * @return string
	 */
	public function getJavascript($sJavascript, $sName) {
		// Create a javascript placeholder
		$sJs = (string) null;
		// Check to see if we are loading one script or batching them
		if (is_array($sJavascript)) { // Batching the scripts
			// Loop through the javascript
			foreach ($sJavascript as $sScript) {
				// Append the script
				$sJs .= (string) Framsie::getInstance()->renderBlock($sScript);
			}
		} else {                      // Only one script
			// Set the JS
			$sJs = (string) Framsie::getInstance()->renderBlock($sJavascript);
		}
		// Return the JS
		return $this->minifyAsset($sJs, $sName);
	}
}
