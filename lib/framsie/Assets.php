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
	 * This method generates a name for the asset to store in cache
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access protected
	 * @param array|string $mAsset
	 * @param boolean $bMinified
	 * @return string
	 */
	protected function generateName($mAsset, $bMinified = true) {
		// Asset type placeholder
		$sType = (string) null;
		// Asset name placeholder
		$sName = (string) null;
		// Determine if we have multiple assets
		if (is_array($mAsset)) {
			// Determine if this is a script or a style
			if (preg_match('/\.css/', $mAsset[0])) {
				// Set the asset type
				$sType = (string) 'style';
			} else {
				// Set the asset type
				$sType = (string) 'script';
			}
			// Loop through the assets
			foreach ($mAsset as $iIndex => $sAsset) {
				// Make sure the asset is not empty
				if (empty($sAsset) === false) {
					// Start the asset
					$sName .= (string) '.';
					// Append to the asset name
					$sName .= $sAsset;
				}
			}
			// Remove all slashes and extensions
			$sName = (string) str_replace(array('/', '.css', '.js'), null, $sName);
			// REmove all non-alphanumeric and non-period characters
			$sName = (string) preg_replace('/[^a-zA-Z0-9\.]+/',      null, $sName);
			// Encode the name
			$sName = (string) sha1($sName);
			// Return the name
			return "asset.{$sType}{$sName}.combined".(($bMinified === true) ? '.min' : null);
		}
		// Determine is this is a script or a style
		if (preg_match('/\.css/', $mAsset)) {
		// Set the asset type
			$sType = (string) 'style';
		} else {
			// Set the asset type
			$sType = (string) 'script';
		}
		// Remove all slashes and extensions
		$sName = (string) str_replace(array('/', '.css', '.js'), null, $mAsset);
		// REmove all non-alphanumeric and non-period characters
		$sName = (string) preg_replace('/[^a-zA-Z0-9\.]+/',      null, $sName);
		// Encode the name
		$sName = (string) sha1($sName);
		// Return the name
		return "asset.{$sType}.{$sName}".(($bMinified === true) ? '.min' : null);
	}

	/**
	 * This method saves the asset into cache and returns
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access protected
	 * @param string $sSource
	 * @param string $sName
	 * @return string
	 */
	protected function loadAsset($sSource, $sName) {
		// Cache the source
		FramsieCache::getInstance()->saveToCache($sName, $sSource);
		// Return the source
		return $sSource;
	}

	/**
	 * This method minifies a javascript or CSS source set and saves it into the cache
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access protected
	 * @param string $sSource
	 * @return string
	 */
	protected function minifyAsset($sSource, $sName, $bIsJs = true) {
		// Set a string placeholder
		$sMinified = (string) $sSource;
		// Check to see if we are minifying JS or CSS
		if ($bIsJs === false) {
			// Remove all single line comments
			$sMinified = (string) preg_replace('/(\/\*[\w\'\s\r\n\*]*\*\/)|(\/\/[\w\s\']*)|(\<![\-\-\s\w\>\/]*\>)/', null, $sMinified);
			// Remove Multi-Line Comments
			$sMinified = (string) preg_replace('#/\*.*?\*/#s', null, $sMinified);
			// Remove Unnecessary Whitespace
			$sMinified = (string) preg_replace('/\s*([{}|:;,])\s+/', '$1', $sMinified);
			// Remove Trailing whitespace
			$sMinified = (string) preg_replace('/\s\s+(.*)/', '$1', $sMinified);
			// Remove Unnecessary semi-colons
			$sMinified = (string) str_replace(';}', '}', $sMinified);
			// Remove all tabs
			$sMinified = (string) preg_replace('/\t+/', ' ', $sMinified);
			// Remove all double spaces
			$sMinified = (string) preg_replace('/\s+/', ' ', $sMinified);
			// Remove all new lines
			$sMinified = (string) preg_replace('/'.PHP_EOL.'/', null, $sMinified);
			// Set the name comments
			$sMinified = (string) str_replace(array('[:::', ':::]'), array("\n\n/* ", " */\n"), $sMinified);
		} else {
			// Remove Multi-Line Comments
			// $sMinified = (string) preg_replace('#/\*.*?\*/#s', null, $sMinified);
			// Remove multiple new lines
			// $sMinified = (string) preg_replace('/\n(\s*\n)+/', "\n", $sMinified);
			// Replace tabs with spaces
			// $sMinified = (string) preg_replace('/\t+/',        ' ',  $sMinified);
			// Replace double spaces with single spaces
			// $sMinified = (string) preg_replace('/\s\s+/',      ' ',  $sMinified);
			// Add the name of the file to the cache file
			// $sMinified = '/** '.$sName.' **/'.$sMinified;
		}
		// Cache the minified source
		FramsieCache::getInstance()->saveToCache($sName, $sMinified);
		// Return the minified source
		return $sMinified;
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
	 * @param array|string $mStylesheet
	 * @param boolean $bMinify
	 * @return string
	 */
	public function getCss($mStylesheet, $bMinify = true) {
		// Create a CSS placeholder
		$sCss = (string) null;
		// Determine if we even need to load the asset
		if (FramsieCache::getInstance()->cacheExists($this->generateName($mStylesheet, $bMinify))) {
			// Return the cached version
			return FramsieCache::getInstance()->loadFromCache($this->generateName($mStylesheet, $bMinify));
		}
		// Check to see if we are loading one stylesheet or batching them
		if (is_array($mStylesheet)) {
			// Loop through the stylesheets
			foreach ($mStylesheet as $sSheet) {
				// Append the stylesheet
				$sCss .= (string) (($bMinify === true) ? "[:::{$sSheet}:::]" : null).Framsie::getInstance()->renderBlock(CSS_ASSETS_PATH.DIRECTORY_SEPARATOR.$sSheet);
			}
		}  else {
			// Set the stylesheet
			$sCss = (string) (($bMinify === true) ? "[:::{$mStylesheet}:::]" : null).Framsie::getInstance()->renderBlock(CSS_ASSETS_PATH.DIRECTORY_SEPARATOR.$mStylesheet);
		}
		// Return the CSS
		return (($bMinify === true) ? $this->minifyAsset($sCss, $this->generateName($mStylesheet, true), false) : $this->loadAsset($sCss, $this->generateName($mStylesheet, false)));
	}

	/**
	 * This method  returns an image
	 * @package Framsie
	 * @subpackage FramsieAssets
	 * @access public
	 * @param string $sImage
	 * @return string
	 */
	public function getImage($sImage) {
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
	 * @param array|string $mJavascript
	 * @param boolean $bMinify
	 * @return string
	 */
	public function getJavascript($mJavascript, $bMinify = true) {
		// Create a javascript placeholder
		$sJs = (string) null;
		// Determine if we even need to load the asset
		if (FramsieCache::getInstance()->cacheExists($this->generateName($mJavascript, $bMinify))) {
			// Return the cached version
			return FramsieCache::getInstance()->loadFromCache($this->generateName($mJavascript, $bMinify));
		}
		// Check to see if we are loading one script or batching them
		if (is_array($mJavascript)) { // Batching the scripts
			// Loop through the javascript
			foreach ($mJavascript as $sScript) {
				// Append the script
				$sJs .= (string) Framsie::getInstance()->renderBlock(JAVASCRIPT_ASSETS_PATH.DIRECTORY_SEPARATOR.$sScript);
			}
		} else {                      // Only one script
			// Set the JS
			$sJs = (string) Framsie::getInstance()->renderBlock(JAVASCRIPT_ASSETS_PATH.DIRECTORY_SEPARATOR.$mJavascript);
		}
		// Return the JS
		return (($bMinify === true) ? $this->minifyAsset($sJs, $this->generateName($mJavascript, true)) : $this->loadAsset($sJs, $this->generateName($mJavascript, false)));
	}
}
