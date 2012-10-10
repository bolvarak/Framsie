<?php

class FramsieImage {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance
	 * @access protected
	 * @staticvar FramsieImage
	 */
	protected static $mInstance = null;

	/**
	 * This property contains an instance of ImageMagick
	 * @access protected
	 * @var Imagick
	 */
	protected $mImageMagick     = null;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method creates an access point for a single instance to be used across the system
	 * @package Framsie
	 * @subpackage FramsieImage
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieImage self::$mInstance
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
	 * This method sets an external instance into the class
	 * @package Framsie
	 * @subpackage FramsieImage
	 * @access public
	 * @static
	 * @param FramsieImage $oInstance
	 * @return FramsieImage self::$mInstance
	 */
	public static function setInstance(FramsieImage $oInstance) {
		// Set the external instance into the class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply instantiates the Imagick class
	 * @package Framsie
	 * @subpackage FramsieImage
	 * @access public
	 * @return FramsieImage $this
	 */
	public function __construct() {
		// Instantiate the Imagick instance
		$this->mImageMagick = new Imagick();
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method resizes an image blob and writes it to the file system
	 * @package Framsie
	 * @subpackage FramsieImage
	 * @access public
	 * @param string $sBlob
	 * @param string $sFileName
	 * @param integer $iWidth
	 * @param integer $iHeight
	 * @throws FramsieException
	 * @return FramsieImage $this
	 */
	public function writeResizedBlob($sBlob, $sFileName, $iWidth, $iHeight) {
		// ob_start();
		// var_dump(func_get_args());
		// throw new Exception(ob_get_clean());
		// Load the blob
		if ($this->mImageMagick->readimageblob("{$sBlob}") === false) {
			// Trigger an exception
			Framsie::Trigger('FRAMURB');
		}
		// Resize the image
		if ($this->mImageMagick->resizeimage($iWidth, $iHeight, Imagick::FILTER_UNDEFINED, false) === false) {
			// Trigger an exception
			Framsie::Trigger('FRAMURI', array($iWidth, $iHeight));
		}
		// Write the image
		if ($this->mImageMagick->writeimage($sFileName) === false) {
			// Trigger an exception
			Framsie::Trigger('FRAMUWI', array($sFileName));
		}
		// Clear the image
		$this->mImageMagick->clear();
		// We're done, return the instance
		return $this;
	}

	/**
	 * This method resizes an already existing filesystem image
	 * @package Framsie
	 * @subpackage FramsieImage
	 * @param string $sCurrentFileName
	 * @param integer $iWidth
	 * @param integer $iHeight
	 * @param string $sNewFileName
	 * @return FramsieImage $this
	 */
	public function writeResizedImage($sCurrentFileName, $iWidth, $iHeight, $sNewFileName) {
		// Instantiate a new IMagick instance
		$oImagick = new Imagick($sCurrentFileName);
		// Resize the image
		if ($oImagick->resizeimage($iWidth, $iHeight, Imagick::FILTER_LANCZOS, true) === false) {
			// Trigger an exception
			Framsie::Trigger('FRAMURI', array($iWidth, $iHeight));
		}
		// Write the image
		if ($oImagick->writeimage(empty($sNewFileName) ? $sCurrentFileName : $sNewFileName) === false) {
			// Trigger an exception
			Framsie::Trigger('FRAMUWI', array((empty($sNewFileName) ? $sCurrentFileName : $sNewFileName)));
		}
		// Clear the image
		$oImagick->clear();
		// Destroy the object
		$oImagick->destroy();
		// We're done, return the instance
		return $this;
	}

	/**
	 * This method create a thumbnail from a larger image
	 * @package Framsie
	 * @subpackage FramsieImage
	 * @access public
	 * @param string $sCurrentFileName
	 * @param integer $iWidth
	 * @param integer $iHeight
	 * @param string $sNewFileName
	 * @return FramsieImage $this
	 */
	public function writeThumbnameImage($sCurrentFileName, $iWidth, $iHeight, $sNewFileName) {
		// Instantiate a new IMagick instance
		$oImagick = new Imagick($sCurrentFileName);
		// Resize the image
		if ($oImagick->cropthumbnailimage($iWidth, $iHeight) === false) {
			// Trigger an exception
			Framsie::Trigger('FRAMURI', array($iWidth, $iHeight));
		}
		// Write the image
		if ($oImagick->writeimage(empty($sNewFileName) ? $sCurrentFileName : $sNewFileName) === false) {
			// Trigger an exception
			Framsie::Trigger('FRAMUWI', array((empty($sNewFileName) ? $sCurrentFileName : $sNewFileName)));
		}
		// Clear the image
		$oImagick->clear();
		// Destroy the object
		$oImagick->destroy();
		// We're done, return the instance
		return $this;
	}

}