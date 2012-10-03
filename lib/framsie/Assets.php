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
		// Load from cache
		$sCachedSource = (string) FramsieCache::getInstance()->loadFromCache($sName);
		// Check the cached source
		if (empty($sCachedSource)) {
			// Buffer the output
			ob_start();
			// Grab the time
			$iTime     = microtime(true);
			// Set the pointer
			$iPointer  = 0;
			// Set a RegEx identifier
			$bRegEx    = false;
			// Set a string placeholder
			$sMinified = (string) null;
			// Set a temporary string placeholder
			$sString   = (string) null;
			// Loop through the source character by character
			while ($iPointer != strlen($sSource)) {
				// Check the pointer for a forward slash
				if ($sSource[$iPointer] === '/') {
					// Assume RegEx
					$bRegEx = true;
					// Check the pointer
					if ($iPointer > 0) {
						// Set the offset
						$iOffset = $iPointer;
						// Loop through the backtrace
						while ($iOffset > 0) {
							// Decriment the offset
							$iOffset--;
							// Verify RegEx
							if (($sSource[$iOffset] === '(') || ($sSource[$iOffset] === ':') || ($sSource[$iOffset] === '=')) { // Check for RegEx
								// Loop back into the source
								while ($iPointer <= strlen($sSource)) {
									// Set a temporary string
									$sString = strstr(substr($sSource, ($iPointer + 1)), '/', true);
									// Check the temporary string
									if (!strlen($sString) && ($sSource[($iPointer - 1)] !== '/') || strpos($sString, "\n")) {
										// This is not a RegEx pattern
										$bRegEx = false;
										// Break the loop
										break;
									}
									// Output the next section of the string
									echo '/'.$sString;
									// Increment the pointer
									$iPointer += (strlen($sString) + 1);
									// Continue pattern matching if "/" is preceded by "\"
									if (($sSource[($iPointer - 1)] !== '\\') || ($sSource[($iPointer - 1)] === '\\')) {
										// Output the next section of the string
										echo '/';
										// Increment the pointer
										$iPointer++;
										// Break the loop
										break;
									}
								}
								// Break the loop
								break;
							} elseif (($sSource[$iOffset] !== "\t") && ($sSource[$iOffset] !== ' ')) {                          // Make sure this isn't a tab or a space
								// This is not a RegEx
								$bRegEx = false;
								// Break the loop
								break;
							}
						}
						// Check the offset
						if ($bRegEx && ($iOffset < 1)) {
							// This is not a RegEx
							$bRegEx = false;
						}
					}
					// Check the RegEx identifier and the pointer value
					if (($bRegEx === false) || ($iPointer < 1)) {
						// Check for a JavaScript DocBlocks
						if (substr($sSource, ($iPointer + 1), 2) === '*@') { // Conditional DocBlock
							// Set the temporary string
							$sString = (string) strstr(substr($sSource, ($iPointer + 3)), '@*/', true);
							// Output the next section of the string
							echo '/*@'.$sString.$sSource[$iPointer].'@*/';
							// Increment the pointer
							$iPointer += (strlen($sString) + 6);
						} elseif ($sSource[($iPointer + 1)] === '*') {       // Check for a comment block
							// Set the temporary string
							$sString = (string) strstr(substr($sSource, ($iPointer + 2)), '*/', true);
							// Increment the pointer
							$iPointer += (strlen($sString) + 4);
						} elseif ($sSource[($iPointer + 1)] === '/') {       // Comment
							// Set the temporary string
							$sString = (string) strstr(substr($sSource, ($iPointer + 2)), "\n", true);
							// Increment the pointer
							$iPointer += (strlen($sString) + 2);
						} else {                                             // Division Operator
							// Output the next part of the string
							echo $sSource[$iPointer];
							// Increment the pointer
							$iPointer++;
						}
					}
					// Continue to the next iteration
					continue;
				} elseif (($sSource[$iPointer] === '\'') || ($sSource[$iPointer] === '"')) { // Strings
					// Grab the match character
					$sMatch = $sSource[$iPointer];
					// Loop through the string
					while ($iPointer <= strlen($sSource)) {
						// Set the temporary string
						$sString = (string) strstr(substr($sSource, ($iPointer + 1)), $sSource[$iPointer], true);
						// Output the next part of the string
						echo $sMatch.$sString;
						// Increment the pointer
						$iPointer += (strlen($sString) + 1);
						// Check for escapes
						if (($sSource[($iPointer - 1)] !== '\\') || ($sSource[($iPointer - 2)] === '\\')) {
							// Output the next part of the string
							echo $sMatch;
							// Increment the pointer
							$iPointer++;
							// Break the loop
							break;
						}
					}
					// Continue to the next iteration
					continue;
				}
				// Check for newlines, double spaces and tabs
				if (($sSource[$iPointer] !== "\r") && ($sSource[$iPointer] !== "\n") && (($sSource[$iPointer] !== "\t") && ($sSource[$iPointer] !== ' ') || preg_match('/[\w\$]/', $sSource[($iPointer - 1)]) && preg_match('/[\w\$]/', $sSource[($iPointer + 1)]))) {
					// Output the next part of the string
					echo str_replace("\t", ' ', $sSource[$iPointer]);
					// Increment the pointer
					$iPointer++;
				}
			}
			// Print the compression time
			echo '/* Compressed By:  Framsie PHP Framework; Compressed In:  '.round((microtime(true) - $iTime), 4).'s */';
			// Grab the minified source
			$sMinified = (string) ob_get_clean();
			// Cache the minified source
			FramsieCache::getInstance()->saveToCache($sName, $sMinified);
			// Return the minified source
			return $sMinified;
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
		return (($bMinify === true) ? $this->minifyAsset($sCss, "{$sName}.min") : $this->loadAsset($sCss, $sName));
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
		return (($bMinify === true) ? $this->minifyAsset($sJs, "{$sName}.min") : $this->loadAsset($sJs, $sName));
	}
}
