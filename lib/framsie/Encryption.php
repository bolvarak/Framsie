<?php
/**
 * This class handles encryption and decryption of entities
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieEncryption {
	
	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This property contains our singleton instance
	 * @staticvar FramsieEncryption
	 */
	protected static $mInstance = null;
	
	/**
	 * This property contains the cipher text
	 * @access protected
	 * @var string
	 */
	protected $mCipher          = null;
	
	/**
	 * This property tells the system what hashing method to use
	 * @access protected
	 * @var string
	 */
	protected $mEncryptionHash  = MCRYPT_RIJNDAEL_256;
	
	/**
	 * This property holds how many times the algorithm will recurse 
	 * through the hash and encryption
	 * @access protected
	 * @var integer
	 */
	protected $mPasses          = 7;
	
	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method is responsible for maintaining a single existing instance
	 * of this class throughout the application
	 * @package Framsie
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieEncryption self::$mInstance
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
	 * This method sets a custom or external instance into this class and
	 * instance, this is generally only used for phpUnit
	 * @package Framsie
	 * @access public
	 * @static
	 * @param FramsieEncryption $oInstance
	 * @return FramsieEncryption self::$mInstance
	 */
	public static function setInstance(FramsieEncryption $oInstance) {
		// Set the new instance into the system
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Public Methods  //////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method recursively decrypts a hash that was encrypted by this class
	 * @package Framsie
	 * @access public
	 * @param string $sHash
	 * @return string
	 */
	public function Decrypt($sHash) {
		// Set the hash placeholder
		$sText = (string) base64_decode($sHash);
		// Recurse through the encryption
		for ($iPass = 0; $iPass < $this->mPasses; $iPass++) {
			// Encrypt the text
			$sText = (string) mcrypt_decrypt(
					$this->mEncryptionHash, // Set the hashing algorithm
					$this->mCipher,         // Set the secret key
					$sText,                 // Set the data to be decrypted
					MCRYPT_MODE_ECB,        // Set the encryption mode
					mcrypt_create_iv(       // Create the algorithm
							mcrypt_get_iv_size($this->mEncryptionHash, MCRYPT_MODE_ECB),
							MCRYPT_RAND
					)
			);
		}
		// Return the clear text
		return trim($sText);
	}
	
	/**
	 * This method runs an encryption algorithm on the provided text and hashes it
	 * @package Framsie
	 * @access public
	 * @param string $sText
	 * @return string
	 */
	public function Encrypt($sText) {
		// Set the hash placeholder
		$sHash = (string) $sText;
		// Recurse through the encryption
		for ($iPass = 0; $iPass < $this->mPasses; $iPass++) {
			// Encrypt the text
			$sHash = (string) mcrypt_encrypt(
				$this->mEncryptionHash, // Set the hashing algorithm
				$this->mCipher,         // Set the secret key
				$sHash,                 // Set the data to be encrypted
				MCRYPT_MODE_ECB,        // Set the encryption mode
				mcrypt_create_iv(       // Create the algorithm
					mcrypt_get_iv_size($this->mEncryptionHash, MCRYPT_MODE_ECB), 
					MCRYPT_RAND
				)
			);
		}
		// Make the hash storable and readable
		return rtrim(base64_encode($sHash), '\0');
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method sets the secret key or cipher to be used for encrypting
	 * the data, this key is sacred so keep it safe
	 * @package Framsie
	 * @subpackage Setters
	 * @access public
	 * @param string $sCipher
	 * @return FramsieEncryption $this
	 */
	public function setCipher($sCipher) {
		// Set the cipher into the system
		$this->mCipher = (string) $sCipher;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the flavor of encryption hashing we should use
	 * @package Framsie
	 * @subpackage Setters
	 * @access public
	 * @param string $sEncryptionHash
	 * @return FramsieEncryption $this
	 */
	public function setEncryptionHash($sEncryptionHash) {
		// Set the encryption hash into the system
		$this->mEncryptionHash = (string) $sEncryptionHash;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets how recursive the encryption is
	 * @package Framsie
	 * @subpackage Setters
	 * @access public
	 * @param integer $iPasses
	 * @return FramsieEncryption $this
	 */
	public function setPasses($iPasses) {
		// Set the number of recursive passes that should be performed
		$this->mPasses = (integer) (($iPasses > 0) ? $iPasses : 1);
		// Return the instance
		return $this;
	}
}
