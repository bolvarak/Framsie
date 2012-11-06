<?php
/**
 * This class provides easy XML encoding and decoding
 * @package Framsie
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class FramsieXml {

	///////////////////////////////////////////////////////////////////////////
	/// Constants ////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This constant contains the value for UTF-8 encodings
	 * @var string
	 */
	const XML_ENCODING_UTF8       = 'UTF-8';

	/**
	 * This constant contains the value for XML version 1.0
	 * @var string
	 */
	const XML_VERSION_1_0         = '1.0';

	///////////////////////////////////////////////////////////////////////////
	/// Public Methods ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method encodes an array or an object into an XML data structure
	 * @package Framsie
	 * @subpackage FramsieXml
	 * @access public
	 * @static
	 * @param array|boolean|integer|object|string $mEntity
	 * @param DOMElement $oDomElement
	 * @param DOMDocument $oDomDocument
	 * @return string
	 */
	public static function Encode($sRootNode, $mEntity, $oDomElement = null, $oDomDocument = null) {
		// Check for a DOMElement
		if (empty($oDomElement)) {
			// Create a new document
			$oDomDocument               = new DOMDocument();
			// We want pretty printing
			$oDomDocument->formatOutput = true;
			// Create the root node
			// $oRootNode                  = $oDomDocument->createElement($sRootNode);
			// Add the root node
			// $oDomDocument->appendChild($oRootNode);
			// Start the execution of this process
			self::Encode($sRootNode, $mEntity, $oDomDocument->documentElement, $oDomDocument);
			// Return the XML
			return $oDomDocument->saveXML();
		}
		// Check for an array or an object
		if (is_array($mEntity) || is_object($mEntity)) {
			// Loop through the elements of the entity
			foreach ($mEntity as $mIndex => $mElement) {
				// Set the root node name
				$sRootNode = $mIndex;
				// Check for a numerically indexed array
				if (is_int($mIndex)) {
					// Check to see if this is the first index
					if ($mIndex === 0) {
						// Set the node
						$oNode     = $oDomElement;
						// Set the root node name
						$sRootNode = $oDomElement->tagName;
					} else {
						// Create the node
						$oNode = $oDomDocument->createElement($oDomElement->tagName);
						// Set the node
						$oDomElement->parentNode->appendChild($oNode);
						// Set the root node name
						$sRootNode = $oDomElement->tagName;
					}
				} else {
					// Create the collection
					$oCollection = $oDomDocument->createElement($mIndex);
					// Set the collection
					$oDomElement->appendChild($oCollection);
					// Create the node
					$oNode       = $oCollection;
					// Check for multiples of this element
					if ((rtrim($mIndex, 's') !== $mIndex) && (count($mElement) > 1)) {
						// Create the collection item
						$oItem = $oDomDocument->createElement(rtrim($mIndex, 's'));
						// Append the item to the collection
						$oCollection->appendChild($oItem);
						// Set the node
						$oNode = $oItem;
						// Set the root node name
						$sRootNode = rtrim($mIndex, 's');
					}
				}
				// Recursively execute this method
				self::Encode($sRootNode, $mElement, $oNode, $oDomDocument);
			}
		} else {
			// Check to see if the entity is a boolean
			if (is_bool($mEntity)) {
				// Reset the entity
				$mEntity = (string) (($mEntity === true) ? 'true' : 'false');
			}
			// Append the entity to the element
			$oDomElement->appendChild($oDomDocument->createTextNode($mEntity));
		}
	}
}
