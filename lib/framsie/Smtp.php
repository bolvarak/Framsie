<?php
/**
 * This class provides an easy SMTP mail sending interface
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieSmtp {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the method number for the AUTH LOGIN request
	 * @var integer
	 */
	const AUTH_LOGIN_REQUEST      = 1;

	/**
	 * This constant contains the method number for the DATA request
	 * @var integer
	 */
	const DATA_REQUEST            = 4;

	/**
	 * This constant contains the method number for the HELO request
	 * @var integer
	 */
	const HELO_REQUEST            = 0;

	/**
	 * This constant contains the method number for the RCPT TO request
	 * @var integer
	 */
	const MAIL_FROM_REQUEST       = 2;

	/**
	 * This constant contains the method number for the MAIL TO request
	 * @var integer
	 */
	const MAIL_TO_REQUEST         = 3;

	/**
	 * This constant contains the MIME-Type value for HTML messages
	 * @var string
	 */
	const MIME_HTML               = 'text/html';

	/**
	 * This constant contains the MIME-Type value for text messages
	 * @var string
	 */
	const MIME_TEXT               = 'text/plain';

	/**
	 * This constants contains the newline type for SMTP servers
	 * @var string
	 */
	const NEWLINE                 = '\r\n';

	/**
	 * This constant contains the method number for the QUIT request
	 * @var integer
	 */
	const QUIT_REQUEST            = 5;

	/**
	 * This constant contains the number of bits to read from the server
	 * @var integer
	 */
	const READ_BITS               = 4096;

	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This property contains the singleton instance of this class
	 * @access protected
	 * @staticvar FramsieSmtp
	 */
	protected static $mInstance   = null;

	/**
	 * This property contains an array of hooks that are executed once the systems logs in
	 * @access protected
	 */
	protected $mAuthHooks         = array();

	/**
	 * This property contains the message
	 * @access protected
	 * @var string
	 */
	protected $mMessage           = null;

	/**
	 * This property contains the MIME-Type for the messag
	 * @access protected
	 * @var string
	 */
	protected $mMimeType          = null;

	/**
	 * This property contains the authentication password
	 * @access protected
	 * @var string
	 */
	protected $mPassword          = null;

	/**
	 * This property contains the recipient email addresses
	 * @access protected
	 * @var array
	 */
	protected $mRecipients        = array();

	/**
	 * This property contains the last response from the server
	 * @access protected
	 * @var string
	 */
	protected $mResponse          = null;

	/**
	 * This property contains the sender's email address
	 * @access protected
	 * @var string
	 */
	protected $mSender            = null;

	/**
	 * This property contains the sender's name
	 * @access protected
	 * @var string
	 */
	protected $mSenderName        = null;

	/**
	 * This property contains the SMTP server's address
	 * @access protected
	 * @var string
	 */
	protected $mServerAddress     = null;

	/**
	 * This proeprty contains the SMTP server's port
	 * @access protected
	 * @var integer
	 */
	protected $mServerPort        = 25;

	/**
	 * This property contains the socket connection
	 * @access protected
	 * @var file pointer
	 */
	protected $mSocket            = null;

	/**
	 * This property contains the subject of the message
	 * @access protected
	 * @var string
	 */
	protected $mSubject           = null;

	/**
	 * This property contains the number of seconds to wait for a connection
	 * @access protected
	 * @var integer
	 */
	protected $mTimeout           = 30;

	/**
	 * This property contains the authentication notifier
	 * @access protected
	 * @var boolean
	 */
	protected $mUseAuthentication = false;

	/**
	 * This property contains the authentication username
	 * @access protected
	 * @var string
	 */
	protected $mUsername          = null;

	/**
	 * This property contains the SSL notifier
	 * @access protected
	 * @var boolean
	 */
	protected $mUseSsl            = false;

	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the singleton instance of this class
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @static
	 * @param boolean $bReset
	 * @return FramsieSmtp self::$mInstance
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
	 * @subpackage FramsieSmtp
	 * @access public
	 * @static
	 * @param FramsieSmtp $oInstance
	 * @return FramsieSmtp self::$mInstance
	 */
	public static function setInstance(FramsieSmtp $oInstance) {
		// Set the external instance into the system
		self::$mInstance = $oInstance;
		// Return the instance
		return self::$mInstance;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor simply returns an instance of this class
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return FramsieSmtp $this
	 */
	public function __contruct() {
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Protected Methods ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This helper method handles the AUTH LOGIN socket request
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @return FramsieSmtp $this
	 */
	protected function authLoginRequest() {
		// Write the request
		fputs($this->mSocket, 'AUTH LOGIN'.self::NEWLINE, self::READ_BITS);
		// Process the response
		$this->readSocket();
		// Write the username
		fputs($this->mSocket, $this->mUsername.self::NEWLINE, self::READ_BITS);
		// Process the response
		$this->readSocket();
		// Write the password
		fputs($this->mSocket, $this->mPassword.self::NEWLINE, self::READ_BITS);
		// Process the response
		$this->readSocket();
		// Return the instance
		return $this;
	}

	/**
	 * Thsi builer method builds the header request
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @param string $sEmailAddress
	 * @return string
	 */
	protected function buildHeaders($sEmailAddress) {
		// Validate the email address
		$this->validateEmailAddress($sEmailAddress, false);
		// Validate the sender email address
		$this->validateEmailAddress($this->mSender, true);
		// Set the MIME-Version
		$sHeaders  = (string) 'MIME-Version: 1.0'.self::NEWLINE;
		// Set the Content-Type
		$sHeaders .= (string) "Content-Type: {$this->mMimeType}; charset=iso-8859-1".self::NEWLINE;
		// Set the recipient
		$sHeaders .= (string) "To: <{$sEmailAddress}>".self::NEWLINE;
		// Set the sender
		if (empty($this->mSenderName)) { // There is no sender name
			// Set the sender
			$sHeaders .= (string) "From: <{$this->mSender}>".self::NEWLINE;
		} else {                        // There is a sender name
			// Set the sender
			$sHeaders .= (string) "From: {$this->mSenderName} <{$this->mSender}>".self::NEWLINE;
		}
		// Return the headers
		return $sHeaders;
	}

	/**
	 * This builder method builds the actual message to be transmitted
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @param string $sEmailAddress
	 * @return string
	 */
	protected function buildMessage($sEmailAddress) {
		// Build the headers
		$sHeaders = (string) $this->buildHeaders($sEmailAddress);
		// Build the message data
		$sMessage  = (string) "To: {$sEmailAddress}".self::NEWLINE;            // Set the recipient
		$sMessage .= (string) "From: {$this->mSender}".self::NEWLINE;          // Set the sender
		$sMessage .= (string) "Subject: {$this->mSubject}".self::NEWLINE;      // Set the subject
		$sMessage .= (string) $sHeaders.self::NEWLINE.self::NEWLINE;           // Set the headers
		$sMessage .= (string) $this->mMessage.self::NEWLINE.'.'.self::NEWLINE; // Set the message
		// Return the message
		return $sMessage;
	}

	/**
	 * This helper method handles the DATA socket request
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @return FramsieSmtp $this
	 */
	protected function dataRequest() {
		// Write the request to the socket
		fputs($this->mSocket, "DATA".self::NEWLINE);
		// Process the response
		$this->readSocket();
		// Loop through the recipient addresses
		foreach ($this->mRecipients as $sEmailAddress) {
			// Build the data message
			$sData    = (string) $this->buildMessage($sEmailAddress);
			// Write the data to the socket
			fputs($this->mSocket, $sData);
			// Process the response
			$this->readSocket();
		}
		// Return the instance
		return $this;
	}

	/**
	 * This helper method handles the HELO socket request
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @return FramsieSmtp $this
	 */
	protected function heloRequest() {
		// Write the request
		fputs($this->mSocket, 'HELO 127.0.0.1'.self::NEWLINE);
		// Process the response
		$this->readSocket();
		// Return the instance
		return $this;
	}

	/**
	 * This helper method handles the MAIL FROM socket request
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @return FramsieSmtp $this
	 */
	protected function mailFromRequest() {
		// Begin the from string
		$sSender = (string) null;
		// Check for a sender name
		if (empty($this->mSenderName) === false) {
			// Set the name
			$sSender .= (string) $this->mSenderName;
		}
		// Validate the email address
		$this->validateEmailAddress($this->mSender, true);
		// Set the sender address
		$sSender .= (string) "<{$this->mSender}>";
		// Write to the socket
		fputs($this->mSocket, "MAIL FROM: <{$sSender}>".self::NEWLINE);
		// Process the response
		$this->readSocket();
		// Return the instance
		return $this;
	}

	/**
	 * This helper method handles the RCPT TO socket request
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @return FramsieSmtp $this
	 */
	protected function mailToRequest() {
		// Loop through the recipient addresses
		foreach ($this->mRecipients as $sEmailAddress) {
			// Validate the email address
			$this->validateEmailAddress($sEmailAddress, false);
			// Write the recipient to the request
			fputs($this->mSocket, "RCPT TO: <{$sEmailAddress}>".self::NEWLINE);
			// Process the response
			$this->readSocket();
		}
		// Return the instance
		return $this;
	}

	/**
	 * This helper method handles the disconnecting from the socket
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @return FramsieSmtp $this
	 */
	protected function quitRequest() {
		// Disconnect from the server
		fputs($this->mSocket, "QUIT".self::NEWLINE);
		// Process the response
		$this->readSocket();
		// Return the instance
		return $this;
	}

	/**
	 * This method handles the reading of the socket
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @throws Exception
	 * @return FramsieSmtp $this
	 */
	protected function readSocket() {
		// Read from the socket
		$this->mResponse = (string) fgets($this->mSocket, self::READ_BITS);
		var_dump($this->mResponse); flush();
		// Check for a socket
		if (empty($this->mSocket)) {
			// Throw an exception
			throw new Exception("The socket connection failed: {$this->Response}");
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method validates email addresses
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @param string $sEmailAddress
	 * @param boolean $bSender
	 * @throws Exception
	 * @return FramsieSmtp $this
	 */
	protected function validateEmailAddress($sEmailAddress, $bSender = false) {
		// Check the email address
		if (filter_var($sEmailAddress, FILTER_VALIDATE_EMAIL) === false) {
			// Throw a new exception
			throw new Exception("The email ".(($bSender === true) ? 'sender' : 'recipient')." address\"{$sEmailAddress}\" is invalid.");
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method routes SMTP socket writing
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access protected
	 * @param integer $iType
	 * @return FramsieSmtp $this
	 */
	protected function writeSocket($iType) {
		// Determine the method to execute
		switch($iType) {
			case self::HELO_REQUEST       : $this->heloRequest();      break; // HELO
			case self::AUTH_LOGIN_REQUEST : $this->authLoginRequest(); break; // AUTH LOGIN
			case self::MAIL_FROM_REQUEST  : $this->mailFromRequest();  break; // MAIL FROM
			case self::MAIL_TO_REQUEST    : $this->mailToRequest();    break; // RCPT TO
			case self::DATA_REQUEST       : $this->dataRequest();      break; // DATA
			case self::QUIT_REQUEST       : $this->quitRequest();      break; // QUIT
		}
		// Return the isntance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds an authentication hook to the system
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param array|callback|string $mHook
	 * @throws FramsieException
	 * @return FramsieSmtp $this
	 */
	public function addAuthenticationHook($mHook) {
		// Check for an array and if we can call the method
		if (is_array($mHook) && (method_exists($mHook[0], $mHook[1]) === false)) {
			// Throw an exception
			FramsieError::Trigger('FRAMICS');
		}
		// Check for the method in this object or if the callback is an anonymous function
		if ((method_exists($this, $mHook) === false) && (is_callable($mHook) === false)) {
			// Throw an exception
			FramsieError::Trigger('FRAMICS');
		}
		// Add the hook
		array_push($this->mAuthHooks, $mHook);
	}

	/**
	 * This method adds a recipient to the system
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param string $sEmailAddress
	 * @return FramsieSmtp $this
	 */
	public function addRecipient($sEmailAddress) {
		// Add the email address
		array_push($this->mRecipients, $sEmailAddress);
		// Return the instance
		return $this;
	}

	/**
	 * This method handles the connecting to the server
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param boolean $bUseConfiguration
	 * @return FramsieSmtp $this
	 */
	public function connect($bUseConfiguration = false) {
		// Check to see if we need to use the configuration
		if ($bUseConfiguration === true) {
			// Set the username
			$this->setUsername     (FramsieConfiguration::Load('smtpSettings.username'));
			// Set the password
			$this->setPassword     (FramsieConfiguration::Load('smtpSettings.password'));
			// Set the server host
			$this->setServerAddress(FramsieConfiguration::Load('smtpSettings.serverAddress'));
			// Set the server port
			$this->setServerPort   (FramsieConfiguration::Load('smtpSettings.serverPort'));
		}
		// Open the socket
		$this->mSocket = fsockopen($this->mServerAddress, $this->mServerPort, $iError, $sError, $this->mTimeout);
		// Check for a connection
		if (!$this->mSocket || (empty($sError) === false)) {
			// Throw an exception
			throw new Exception($sError, $iError);
		}
		// Process the response
		$this->readSocket();
		// Send the HELO
		$this->writeSocket(self::HELO_REQUEST);
		// Return the instance
		return $this;
	}

	/**
	 * This method handles the server login
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return FramsieSmtp $this
	 */
	public function login() {
		// Check to see if we need to use authentication
		if ($this->mUseAuthentication === true) {
			// Send the login
			$this->writeSocket(self::AUTH_LOGIN_REQUEST);
		}
		// Return the instance
		return $this;
	}

	/**
	 * This method handles the actual sending of the mail
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return FramsieSmtp $this
	 */
	public function send() {
		// Execute the MAIL FROM
		$this->writeSocket(self::MAIL_FROM_REQUEST);
		// Handle the RCPT TO
		$this->writeSocket(self::MAIL_TO_REQUEST);
		// Handle the DATA
		$this->writeSocket(self::DATA_REQUEST);
		// Disconnect from the server
		$this->writeSocket(self::QUIT_REQUEST);
		// Close the socket
		fclose($this->mSocket);
		// Return the instance
		return $this;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method returns the message
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return string
	 */
	public function getMessage() {
		// Return the message
		return $this->mMessage;
	}

	/**
	 * This method returns the message's MIME-Type
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return string
	 */
	public function getMimeType() {
		// Return the MIME-Type
		return $this->mMimeType;
	}

	/**
	 * This method returns the authentication password
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return string
	 */
	public function getPassword() {
		// Return the password
		return $this->mPassword;
	}

	/**
	 * This method returns the recipient addresses
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return array
	 */
	public function getRecipients() {
		// Return the recipients
		return $this->mRecipients;
	}

	/**
	 * This method returns the sender's address
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return string
	 */
	public function getSender() {
		// Return the sender
		return $this->mSender;
	}

	/**
	 * This method returns the sender's name
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return string
	 */
	public function getSenderName() {
		// Return the sender's name
		return $this->mSenderName;
	}

	/**
	 * This method returns the remote server's address
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return string
	 */
	public function getServerAddress() {
		// Return the server address
		return $this->mServerAddress;
	}

	/**
	 * This method returns the remote server's port
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return integer
	 */
	public function getServerPort() {
		// Return the server port
		return $this->mServerPort;
	}

	/**
	 * This method returns the socket file pointer
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return file
	 */
	public function getSocket() {
		// Return the socket
		return $this->mSocket;
	}

	/**
	 * This method returns the message subject
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return string
	 */
	public function getSubject() {
		// Return the subject
		return $this->mSubject;
	}

	/**
	 * This method returns the number of timeout sections
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return integer
	 */
	public function getTimeout() {
		// Return the current timeout
		return $this->mTimeout;
	}

	/**
	 * This method returns the authentication notifier
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return boolean
	 */
	public function getUseAuthentication() {
		// Return the authentication notifier
		return $this->mUseAuthentication;
	}

	/**
	 * This method returns the authentication username
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return string
	 */
	public function getUsername() {
		// Return the username
		return $this->mUsername;
	}

	/**
	 * This method returns the SSL notifier
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @return boolean
	 */
	public function getUseSsl() {
		// Return the SSL notifier
		return $this->mUseSsl;
	}

	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method sets the message's text
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param string $sMessage
	 * @return FramsieSmtp $this
	 */
	public function setMessage($sMessage) {
		// Set the message
		$this->mMessage = (string) $sMessage;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the message's MIME-Type
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param string $sMimeType
	 * @return FramsieSmtp $this
	 */
	public function setMimeType($sMimeType) {
		// Set the MIME-Type for the message
		$this->mMimeType = (string) $sMimeType;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the authentication password
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param string $sPassword
	 * @return FramsieSmtp $this
	 */
	public function setPassword($sPassword) {
		// Set the password into the system
		$this->mPassword = (string) base64_encode($sPassword);
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the recipient addresses into the system
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param array $aRecipients
	 * @return FramsieSmtp $this
	 */
	public function setRecipients($aRecipients) {
		// Set the recipients
		$this->mRecipients = (array) $aRecipients;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the sender's address
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param string $sEmailAddress
	 * @return FramsieSmtp $this
	 */
	public function setSender($sEmailAddress) {
		// Set the sender's address
		$this->mSender = (string) $sEmailAddress;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the sender's human name
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param string $sName
	 * @return FramsieSmtp $this
	 */
	public function setSenderName($sName) {
		// Set the sender's name
		$this->mSenderName = (string) $sName;
		// Return the instance
		return $this;
	}

	/**
	 * This methos sets the remote server's address
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param string $sAddress
	 * @return FramsieSmtp $this
	 */
	public function setServerAddress($sAddress) {
		// Set the server's address
		$this->mServerAddress = (string) $sAddress;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the remote server's port
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param integer $iPort
	 * @return FramsieSmtp $this
	 */
	public function setServerPort($iPort) {
		// Set the server's port
		$this->mServerPort = (integer) $iPort;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the socket connection
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param file pointer $rSocket
	 * @return FramsieSmtp $this
	 */
	public function setSocket($rSocket) {
		// Set the socket file pointer
		$this->mSocket = $rSocket;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the message's subject
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param string $sSubject
	 * @return FramsieSmtp $this
	 */
	public function setSubject($sSubject) {
		// Set the subject
		$this->mSubject = (string) strip_tags($sSubject);
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the number of sections until the connection times out
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param integer $iSeconds
	 * @return FramsieSmtp $this
	 */
	public function setTimeout($iSeconds) {
		// Set the timeout seconds
		$this->mTimeout = (integer) $iSeconds;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the authentication notification
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param boolean $bNotification
	 * @return FramsieSmtp $this
	 */
	public function setUseAuthentication($bNotification) {
		// Set the authentication notifier
		$this->mUseAuthentication = (boolean) $bNotification;
		// Return the instance
		return $this;
	}

	/**
	 * This method sets the authentication username
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param string $sUsername
	 * @return FramsieSmtp $this
	 */
	public function setUsername($sUsername) {
		// Set the username
		$this->mUsername = (string) base64_encode($sUsername);
		// Return the instance
		return $this;
	}

	/**
	 * This method tells the system whether or not to use SSL
	 * @package Framsie
	 * @subpackage FramsieSmtp
	 * @access public
	 * @param boolean $bNotification
	 * @return FramsieSmtp $this
	 */
	public function setUseSsl($bNotification) {
		// Set the SSL notification notifier
		$this->mUseSsl = (boolean) $bNotification;
		// Return the instance
		return $this;
	}
}
