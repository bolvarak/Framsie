<?php
/**
 * This class provides access to dynamic pages from the database
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieDynamicPage {
	
	///////////////////////////////////////////////////////////////////////////
	/// Properties ///////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This property contains the singleton instance of the class
	 * @access protected
	 * @staticvar FramsieDynamicPage
	 */
	protected static $mInstance                = null;
	
	/**
	 * This property tells the framework that only authorized users may
	 * view the page loaded in this class
	 * @access protected
	 * @var boolean
	 */
	protected $mAuthenticationIsRequired       = false;
	
	/**
	 * This property tells the framework what column to look for the
	 * AuthenticationIsRequired value in
	 * @access protected
	 * @var string
	 */
	protected $mAuthenticationIsRequiredColumn = 'RequireAuthentication';
	
	/**
	 * This property contains the block file that should be loaded
	 * @access protected
	 * @var string
	 */
	protected $mBlock                          = null;
	
	/**
	 * This property tells the framework what column to look for the 
	 * Block value in
	 * @access protected
	 * @var string
	 */
	protected $mBlockColumn                    = 'Block';
	
	/**
	 * This property contains the content for the dynamic page
	 * @access protected
	 * @var string
	 */
	protected $mContent                        = null;
	
	/**
	 * This property tells the framework which column to look for the 
	 * Content value in
	 * @access protected
	 * @var string
	 */
	protected $mContentColumn                  = 'Content';
	
	/**
	 * This property contains the timestamp in which the page was created
	 * @access protected
	 * @var timestamp
	 */
	protected $mCreated                        = null;
	
	/**
	 * This property tells the framework which column to look for the 
	 * Created value in
	 * @access protected
	 * @var string
	 */
	protected $mCreatedColumn                  = 'Created';
	
	/**
	 * This property contains the layout that the page uses in 
	 * relation to the blocks/templates folder
	 * @access protected
	 * @var string
	 */
	protected $mLayout                         = null;
	
	/**
	 * This property tells the framework where to look for the
	 * Layout value in
	 * @access protected
	 * @var string
	 */
	protected $mLayoutColumn                   = 'Layout';
	
	/**
	 * This property contains the unique ID of the page
	 * @access protected
	 * @var integer
	 */
	protected $mId                             = null;
	
	/**
	 * This property tells the framework which column to look for the
	 * ID value in
	 * @access protected
	 * @var string
	 */
	protected $mIdColumn                       = 'PageId';
	
	/**
	 * This property contains the meta tags associated with the current page
	 * @access protected
	 * @var array
	 */
	protected $mMetaTags                       = array();
	
	/**
	 * This property tells the framework where to look for the 
	 * MetaTags value in
	 * @access protected
	 * @var string
	 */
	protected $mMetaTagsColumn                 = 'MetaTags';
	
	/**
	 * This property contains the scripts associated with the current page
	 * @access protected
	 * @var array
	 */
	protected $mScripts                        = array();
	
	/**
	 * This property tells the framework which column to look for the 
	 * Scripts value in
	 * @access protected
	 * @var string
	 */
	protected $mScriptsColumn                  = 'Scripts';
	
	/**
	 * This property contains the styles associated with the current page
	 * @access protected
	 * @var array
	 */
	protected $mStyles                         = array();
	
	/**
	 * This property tells the framework which column to look for the 
	 * Styles value in
	 * @access protected
	 * @var string
	 */
	protected $mStylesColumn                   = 'Styles';
	
	/**
	 * This property tells the framework which table the pages are stored in
	 * @access protected
	 * @var string
	 */
	protected $mTable                          = 'Pages';
	
	/**
	 * This proeprty contains the page's title
	 * @access protected
	 * @var string
	 */
	protected $mTitle                          = null;
	
	/**
	 * This property tells the framework which column to look for the
	 * Title value in
	 * @access protected
	 * @var string
	 */
	protected $mTitleColumn                    = 'Title';
	
	/**
	 * This property contains the timestamp in which the page was last updated
	 * @access protected
	 * @var timestamp
	 */
	protected $mUpdated                        = null;
	
	/**
	 * This property tells the framework which column to look for the
	 * Updated value in
	 * @var string
	 */
	protected $mUpdatedColumn                  = 'Updated';
	
	///////////////////////////////////////////////////////////////////////////
	/// Singleton ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This property provides access to the singleton instance of the class
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @static
	 * @param boolean [$bReset]
	 * @return FramsieDynamicPage self::$mInstance
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
	 * This method sets an external instance into the class, it is generally 
	 * only used in testing and primarily with phpUnit
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @static
	 * @param FramsieDynamicPage $oInstance
	 * @return FramsieDynamicPage self::$mInstance
	 */
	public static function setInstance(FramsieDynamicPage $oInstance) {
		// Set the external instance into the class
		self::$mInstance = $oInstance;
		// Return the instance
		return $this;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * The constructor simply returns the instance, it is public to allow
	 * extension of the class for specific pages
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return FramsieDynamicPage $this
	 */
	public function __constructor() {
		// Return the instance
		return $this;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method loads a page object from the database and populates the 
	 * instance with the page data
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @throws Exception
	 * @return FramsieDynamicPage $this
	 */
	public function loadPage() {
		// Check for a page ID
		if (empty($this->mId)) {
			// Throw an exception, because we need a page ID
			throw new Exception('No page ID was found.  A page ID is required to use this class.');
		}
		// Check for a table
		if (empty($this->mTable)) {
			// Throw an exception because we need a table
			throw new Exception('No table was found.  A table is required to use this class.');
		}
		// Grab the page
		$oPage = FramsieDatabaseInterface::getInstance()                             // Instantiate the DBI
			->setTable($this->mTable)                                                // Set the table
			->setQuery(FramsieDatabaseInterface::SELECTQUERY)                        // We want to build a SELECT query
			->addField($this->mAuthenticationIsRequiredColumn)                       // Add the authentication switch column
			->addField($this->mBlockColumn)                                          // Add the block column
			->addField($this->mContentColumn)                                        // Add the content column
			->addField($this->mCreatedColumn)                                        // Add the created column
			->addField($this->mLayoutColumn)                                         // Add the layout column
			->addField($this->mMetaTagsColumn)                                       // Add the meta tags column
			->addField($this->mScriptsColumn)                                        // Add the scripts column
			->addField($this->mStylesColumn)                                         // Add the styles column
			->addField($this->mTitleColumn)                                          // Add the title column
			->addField($this->mUpdatedColumn)                                        // Add the updated column
			->addWhere($this->mIdColumn, $this->mId, FramsieDatabaseInterface::EQOP) // Add the WHERE clause
			->generateQuery()                                                        // Generate the query
			->getRow();                                                              // Fetch the row
		// Set the page into the system
		$this->setAuthenticationIsRequired($oPage->{$this->mAuthenticationIsRequiredColumn}); // Set the authentication switch
		$this->setBlock                   ($oPage->{$this->mBlockColumn});                    // Set the block
		$this->setContent                 ($oPage->{$this->mContentColumn},  true);           // Set the content
		$this->setCreated                 ($oPage->{$this->mCreatedColumn},  true);           // Set the creation timestamp
		$this->setId                      ($oPage->{$this->mIdColumn});                       // Set the ID
		$this->setLayout                  ($oPage->{$this->mLayoutColumn});                   // Set the layout
		$this->setMetaTags                ($oPage->{$this->mMetaTagsColumn}, true);           // Set the meta tags
		$this->setScripts                 ($oPage->{$this->mScriptsColumn},  true);           // Set the scripts
		$this->setStyles                  ($oPage->{$this->mStylesColumn},   true);           // Set the styles
		$this->setTitle                   ($oPage->{$this->mTitleColumn});                    // Set the title
		$this->setUpdated                 ($oPage->{$this->mUpdatedColumn},  true);           // Set the updated timestamp
		// Return the instance
		return $this;
	}
	
	/**
	 * This method saves a page using the data set in the instance
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @throws Exception
	 * @return FramsieDynamicPage $this
	 */
	public function savePage() {
		// Check for a table
		if (empty($this->mTable)) {
			// Throw an exception because we need a table
			throw new Exception('No table was found.  A table is required to use this class.');
		}
		// Check for a page ID
		if (empty($this->mId)) {
			$bPage = FramsieDatabaseInterface::getInstance()          // Instantiate the DBI
				->setTable($this->mTable)                             // Set the table
				->setQuery(FramsieDatabaseInterface::INNERJOINQUERY); // We want to build an INSERT query
		} else {
			$bPage = FramsieDatabaseInterface::getInstance()                                    // Instantiate the DBI
				->setTable($this->mTable)                                                       // Set the table
				->setQuery(FramsieDatabaseInterface::UPDATEQUERY)                               // We want to build an UPDATE query
				->addWhereClause($this->mIdColumn, $this->mId, FramsieDatabaseInterface::EQOP); // Add the WHERE clause
		}
		// Grab the page
		$bPage
			->addField($this->mAuthenticationIsRequiredColumn, $this->mAuthenticationIsRequired) // Add the authentication switch column
			->addField($this->mBlockColumn,                    $this->mBlock)                    // Add the block column
			->addField($this->mContentColumn,                  $this->mContent)                  // Add the content column
			->addField($this->mCreatedColumn,                  $this->mCreated)                  // Add the created column
			->addField($this->mLayoutColumn,                   $this->mLayout)                   // Add the layout column
			->addField($this->mMetaTagsColumn,                 $this->mMetaTags)                 // Add the meta tags column
			->addField($this->mScriptsColumn,                  $this->mScripts)                  // Add the scripts column
			->addField($this->mStylesColumn,                   $this->mStyles)                   // Add the styles column
			->addField($this->mTitleColumn,                    $this->mTitle)                    // Add the title column
			->addField($this->mUpdatedColumn,                  $this->mUpdated)                  // Add the updated column
			->generateQuery()                                  // Generate the query
			->getQueryExecutionStatus();                       // Execute the query
		// Check the execution status
		if ($bPage === false) {
			// Throw a new exception
			throw new Exception("The page with the title \"{$this->mTitle}\" could not be saved.");
		}
		// Check for an ID
		if (empty($this->mId)) {
			// Set the new ID
			$this->mId = (integer) FramsieDatabaseInterface::getInstance()->getConnection()->lastInsertId();
		}
		// Return the instance
		return $this;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Getters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method returns the current authentication switch
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return boolean
	 */
	public function getAuthenticationIsRequired() {
		// Return the current authentication switch
		return $this->mAuthenticationIsRequired;
	}
	
	/**
	 * This method returns the current authentication switch column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getAuthenticationIsRequiredColumn() {
		// Return the authentication switch column
		return $this->mAuthenticationIsRequiredColumn;
	}
	
	/**
	 * This method returns the current block file
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getBlock() {
		// Return the block
		return $this->mBlock;
	}
	
	/**
	 * This method returns the current block column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getBlockColumn() {
		// Return the block column
		return $this->mBlockColumn;
	}
	
	/**
	 * This method returns the current page content
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param boolean $bConvert
	 * @return string
	 */
	public function getContent($bConvert = true) {
		// Return the current page content
		return (($bConvert === true) ? htmlspecialchars_decode($this->mContent) : $this->mContent);
	}
	
	/**
	 * This method returns the current content column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getContentColumn() {
		// Return the content column
		return $this->mContentColumn;
	}
	
	/**
	 * This method returns the current creation timestamp
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return timestamp
	 */
	public function getCreated() {
		// Return the current created timestamp
		return $this->mCreated;
	}
	
	/**
	 * This method returns the current created timestamp column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getCreatedColumn() {
		// Return the created column
		return $this->mCreatedColumn;
	}
	
	/**
	 * This method returns the current page layout
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getLayout() {
		// Return the current layout
		return $this->mLayout;
	}
	
	/**
	 * This method returns the current layout column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getLayoutColumn() {
		// Return the layout column
		return $this->mLayoutColumn;
	}
	
	/**
	 * This method returns the current page ID
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return integer
	 */
	public function getId() {
		// Return the current page ID
		return $this->mId;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getIdColumn() {
		// Return the page ID column
		return $this->mIdColumn;
	}
	
	/**
	 * This method returns the current meta tags
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param boolean $bDeserialize
	 * @return array<stdClass>
	 */
	public function getMetaTags($bDeserialize = true) {
		// Return the current meta tags
		return (($bDeserialize === true) ? unserialize($this->mMetaTags) : $this->mMetaTags);
	}
	
	/**
	 * This method returns the meta tags column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getMetaTagsColumn() {
		// Return the meta tags column
		return $this->mMetaTagsColumn;
	}
	
	/**
	 * This method returns the current scripts
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param boolean $bDeserialize
	 * @return array<stdClass>
	 */
	public function getScripts($bDeserialize = true) {
		// Return the current scripts
		return (($bDeserialize === true) ? unserialize($this->mScripts) : $this->mScripts);
	}
	
	/**
	 * This method returns the scripts column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getScriptsColumn() {
		// Return the scripts column
		return $this->mScriptsColumn;
	}
	
	/**
	 * This method returns the current styles
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param boolean $bDeserialize
	 * @return array<stdClass>
	 */
	public function getStyles($bDeserialize = true) {
		// Return the current styles
		return (($bDeserialize === true) ? unserialize($this->mStyles) : $this->mStyles);
	}
	
	/**
	 * This method returns the styles column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getStylesColumn() {
		// Return the styles column
		return $this->mStylesColumn;
	}
	
	/**
	 * This method returns the current title
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getTitle() {
		// Return the current title
		return $this->mTitle;
	}
	
	/**
	 * This method returns the title column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getTitleColumn() {
		// Return the title column
		return $this->mTitleColumn;
	}
	
	/**
	 * This method returns the current updated timestamp
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return timestamp
	 */
	public function getUpdated() {
		// Return the current updated timestamp
		return $this->mUpdated;
	}
	
	/**
	 * This method returns the updated timestamp column
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @return string
	 */
	public function getUpdatedColumn() {
		// Return the updated column
		return $this->mUpdatedColumn;
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Setters //////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	/**
	 * This method sets the authentication switch into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param boolean $bYesNo
	 * @return FramsieDynamicPage $this
	 */
	public function setAuthenticationIsRequired($bYesNo) {
		// Set the authentication switch
		$this->mAuthenticationIsRequired = (boolean) $bYesNo;
		// Return the instance
		return $this;
	}
	
	/**
	 * This methos sets the authentication switch column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setAuthenticationIsRequiredColumn($sColumn) {
		// Set the authentication switch column
		$this->mAuthenticationIsRequiredColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the view block file into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sBlockFilename
	 * @return FramsieDynamicPage $this
	 */
	public function setBlock($sBlockFilename) {
		// Set the block
		$this->mBlock = (string) $sBlockFilename;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the view block file column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setBlockColumn($sColumn) {
		// Set the block column
		$this->mBlockColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the content into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sText
	 * @param boolean $bSanitize
	 * @return FramsieDynamicPage $this
	 */
	public function setContent($sText, $bSanitize = false) {
		// Set the content
		$this->mContent = (string) (($bSanitize === true) ? htmlspecialchars($sText) : htmlspecialchars_decode($sText));
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the content column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setContentColumn($sColumn) {
		// Set the content column
		$this->mContentColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the creation timestamp into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param integer $sTimestamp
	 * @param boolean $bConvert
	 * @return FramsieDynamicPage $this
	 */
	public function setCreated($sTimestamp, $bConvert = false) {
		// Set the created timestamp
		$this->mCreated = (integer) (($bConvert === true) ? strtotime($sTimestamp) : $sTimestamp);
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the creation timestamp column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setCreatedColumn($sColumn) {
		// Set the created column
		$this->mCreatedColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the page layout into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sBlockFilename
	 * @return FramsieDynamicPage $this
	 */
	public function setLayout($sBlockFilename) {
		// Set the layout block
		$this->mLayout = (string) $sBlockFilename;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the page layout column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setLayoutColumn($sColumn) {
		// Set the layout column
		$this->mLayoutColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the page ID into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param integer $iPageId
	 * @return FramsieDynamicPage $this
	 */
	public function setId($iPageId) {
		// Set the page ID
		$this->mId = (integer) $iPageId;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the page ID column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setIdColumn($sColumn) {
		// Set the page ID column
		$this->mIdColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the meta tags into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param array<stdClass> $aMetaTags
	 * @param boolean $bDeserialize
	 * @return FramsieDynamicPage
	 */
	public function setMetaTags($aMetaTags, $bDeserialize = false) {
		// Set the meta tags into the system
		$this->mMetaTags = (array) (($bDeserialize === true) ? unserialize($aMetaTags) : serialize($aMetaTags));
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the meta tags column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setMetaTagsColumn($sColumn) {
		// Set the meta tags column
		$this->mMetaTagsColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the scripts into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param array<stdClass> $aScripts
	 * @param boolean $bDeserialize
	 * @return FramsieDynamicPage $this
	 */
	public function setScripts($aScripts, $bDeserialize = false) {
		// Set the scripts
		$this->mScripts = (array) (($bDeserialize === true) ? unserialize($aScripts) : serialize($aScripts));
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the scripts column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setScriptsColumn($sColumn) {
		// Set the scripts column
		$this->mScriptsColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the styles into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param array<stdClass> $aStyles
	 * @param boolean $bDeserialize
	 * @return FramsieDynamicPage $this
	 */
	public function setStyles($aStyles, $bDeserialize = false) {
		// Set the styles
		$this->mStyles = (array) (($bDeserialize === true) ? unserialize($aStyles) : serialize($aStyles));
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the styles column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setStylesColumn($sColumn) {
		// Set the styles column
		$this->mStylesColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the table into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sTable
	 * @return FramsieDynamicPage $this
	 */
	public function setTable($sTable) {
		// Set the table
		$this->mTable = (string) $sTable;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the title into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sTitle
	 * @return FramsieDynamicPage $this
	 */
	public function setTitle($sTitle) {
		// Set the title
		$this->mTitle = (string) $sTitle;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the title column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setTitleColumn($sColumn) {
		// Set the title column
		$this->mTitleColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the updated timestamp into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param integer $iTimestamp
	 * @param boolean $bConvert
	 * @return FramsieDynamicPage $this
	 */
	public function setUpdated($iTimestamp, $bConvert = false) {
		// Set the updated timestamp
		$this->mUpdated = (integer) (($bConvert === true) ? strtotime($iTimestamp) : $iTimestamp);
		// Return the instance
		return $this;
	}
	
	/**
	 * This method sets the updated timestamp column into the system
	 * @package Framsie
	 * @subpackage FramsieDynamicPage
	 * @access public
	 * @param string $sColumn
	 * @return FramsieDynamicPage $this
	 */
	public function setUpdatedColumn($sColumn) {
		// Set the updated column
		$this->mUpdatedColumn = (string) $sColumn;
		// Return the instance
		return $this;
	}
}
