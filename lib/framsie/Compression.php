<?php
/**
 * This class provides an interface into data compression
 * @package Framsie
 * @subpackage FramsieCompression
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieCompression {

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance
	 * @access protected
	 * @staticvar FramsieCompression
	 */
	protected static $mInstance = null;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method creates a single instance of the class to use throughout the system
	 * @package Framsie
	 * @subpackage FramsieCompression
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieCompression self::$mInstance
	 */
	public static function getInstance($bReset = false) {
		// Check for an existing instance or reset notification
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
	 * @subpackage FramsieCompression
	 * @access public
	 * @static
	 * @param FramsieCompression $oInstance
	 * @return FramsieCompression self::$mInstance
	 */
	public static function setInstance(FramsieCompression $oInstance) {
		// Set the instance into the class
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method compresses an array, object or string
	 * @package Framsie
	 * @subpackage FramsieCompression
	 * @access public
	 * @param array|boolean|integer|object|string $mEntity
	 * @param boolean $bEncode
	 * @return string
	 */
	public function compressEntity($mEntity, $bEncode = true) {
		// Check to see if this is an array or an object
		if (is_array($mEntity) || is_object($mEntity)) {
			// Serialize the entity
			$mEntity = (string) serialize($mEntity);
			// Prepend a notification
			$mEntity = (string) '{:SERIALIZED:}'.$mEntity;
		}
		// Compress the entity
		$mEntity = gzdeflate($mEntity, 9);
		// Return the entity
		return (($bEncode === true) ? base64_encode($mEntity) : $mEntity);
	}

	/**
	 * This method decompresses an entity that was compressed by this class
	 * @package Framsie
	 * @subpackage FramsieCompression
	 * @access public
	 * @param string $sCompressed
	 * @param boolean $bDecode
	 * @return array|boolean|float|integer|object|string;
	 */
	public function decompressEntity($sCompressed, $bDecode = true) {
		// Check to see if we need to decode the compressed entity
		if ($bDecode === true) {
			// Decode the entity
			$sCompressed = base64_decode($sCompressed);
		}
		// Decompress the entity
		$sEntity = (string) gzinflate($sCompressed);
		// Check for serialization
		if (preg_match('/\{\:SERIALIZED\:\}/i', $sEntity)) {
			// Return the deserialized entity
			return unserialize(preg_replace('/\{\:SERIALIZED\:\}/i', null, $sEntity));
		}
		// Return the string
		return FramsieConverter::StringToPhpType($sEntity);
	}

	/**
	 * This method recursively compresses the data to $iRecursions
	 * @package Framsie
	 * @subpackage FramsieCompression
	 * @access public
	 * @param array|boolean|float|integer|object|string $mEntity
	 * @param integer $iRecurions
	 * @param boolean $bEncode
	 * @return string
	 */
	public function recursivelyCompressEntity($mEntity, $iRecurions = 2, $bEncode = true) {
		// Create an entity placeholder
		$sEntity = (string) $this->compressEntity($mEntity, false);
		// Make sure the recursions are greater one
		if ($iRecurions > 1) {
			// Loop through the recursions
			for ($iRecursion = 1; $iRecursion < $iRecurions; $iRecursion++) {
				// Compress the entity
				$sEntity = $this->compressEntity($sEntity, false);
			}
		}
		// Return the compressed entity
		return (($bEncode === true) ? base64_encode($sEntity) : $sEntity);
	}

	/**
	 * This method recursively decompresses a string to $iRecursions that has been compressed with this class
	 * @package Framsie
	 * @subpackage FramsieCompression
	 * @access public
	 * @param string $sCompressed
	 * @param integer $iRecursions
	 * @param boolean $bDecode
	 * @return array|boolean|float|integer|number|object|string
	 */
	public function recursivelyDecompressEntity($sCompressed, $iRecursions = 2, $bDecode = true) {
		// Create an entity placeholder
		$mEntity = $this->decompressEntity($sCompressed, $bDecode);
		// Make sure the recursions are greater than one
		if ($iRecursions > 1) {
			// Loop through the recursions
			for ($iRecursion = 1; $iRecursion < $iRecursions; $iRecursion++) {
				// Decompress the entity
				$mEntity = $this->decompressEntity($mEntity, false);
			}
		}
		// Return the decompressed entity
		return $mEntity;
	}
}
