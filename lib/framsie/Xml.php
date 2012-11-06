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
	/// Public Static Methods ////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This method encodes an array or an object into an XML data structure
	 * @package Framsie
	 * @subpackage FramsieXml
	 * @access public
	 * @static
	 * @param array|boolean|integer|object|string $mEntity
	 * @param DOMDocument $oDomElement
	 * @param DOMDocument $oDomDocument
	 * @return string
	 */
	public static function Encode($mEntity, &$oDomElement = null, &$oDomDocument = null) {
		// Check for a DOM document
		if (empty($oDomDocument)) {
			// Create a new DOM document
			$oDomDocument = new DOMDocument(self::XML_VERSION_1_0, self::XML_ENCODING_UTF8);
			// Encode the entity
			self::Encode($mEntity, $oDomDocument, $oDomDocument);
			// Return the XML
			return $oDomDocument->saveXML();
		}
		// Check to see if the entity is an array or an object
		if (is_array($mEntity) || is_object($mEntity)) {
			// Iterate through the entity
			foreach ($mEntity as $mIdentifer => $mElement) {
				// Create a node placeholder
				$oNode = null;
				// Check for a numeric integer
				if (is_numeric($mIdentifer)) {
					// Check for a zero index
					if ($mIdentifer == 0) {
						// Set the node
						$oNode = $oDomElement;
					} else {
						// Set the node
						$oNode = $oDomDocument->createElement($oDomElement->tagName);
						// Append the node
						$oDomElement->parentNode->appendChild($oNode);
					}
				} else {
					// Create the collection
					$oCollection = $oDomDocument->createElement($mIdentifer);
					// Add the collection to the element
					$oDomElement->appendChild($oCollection);
					// Set the collection into the node
					$oNode       = $oCollection;
					// Check the index to see if it's a collection item
					if ((rtrim($mIdentifer, 's') !== $mIdentifer) && (count($mElement) > 1)) {
						// Create the item
						$oItem = $oDomDocument->createElement(rtrim($mIdentifer, 's'));
						// Append the item to the collection
						$oCollection->appendChild($oItem);
						// Set the item into the node
						$oNode = $oItem;
					}
				}
				// Re-run the encoder
				self::Encode($mElement, $oNode, $oDomDocument);
			}
		} else {
			// Check the entity for a boolean
			$mEntity = (is_bool($mEntity) ? ($mEntity ? 'true' : 'false') : $mEntity);
			// Set the child into the element
			$oDomElement->appendChild($oDomDocument->createTextNode($mEntity));
		}
	}
}
