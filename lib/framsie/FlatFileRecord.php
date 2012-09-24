<?php
/**
 * This class maps a FramsieFlatFileInterface record to a class object
 * @package Framsie
 * @subpackage FramsieFlatFileRecord
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieFlatFileRecord {

	///////////////////////////////////////////////////////////////////////////
	/// Constructor //////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * The constructor sets up a record array into the instance
	 * @package Framsie
	 * @subpackage FramsieFlatFileRecord
	 * @access public
	 * @param array $aRecord
	 * @return FramsieFlatFileRecord $this
	 */
	public function __construct($aRecord) {
		// Loop through the record
		foreach ($aRecord as $sColumn => $mValue) {
			// Set the property into the instance
			$this->{$sColumn} = $mValue;
		}
		// Return the instance
		return $this;
	}
}
