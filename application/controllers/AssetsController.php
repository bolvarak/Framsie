<?php
/**
 * This class provides the structure for providing static assets
 * @package Framsie
 * @subpackage AssetsController
 * @version 1.0
 * @copyright 2012 Travis Brown <tmbrown6@gmail.com>
 * @license GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @author Travis Brown <tmbrown6@gmail.com>
 */
class AssetsController extends FramsieController {

	///////////////////////////////////////////////////////////////////////////
	/// View Methods /////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * This view method renders a blob to the user
	 * @package Framsie
	 * @subpackage AssetsController
	 * @access public
	 * @return void
	 */
	public function blobView() {
		// Disable the layout
		$this->setDisableLayout();
		// Disable the view
		$this->getView()->setDisableView();
		// Decode the Mime-Type
		$sMimeType = base64_decode($this->getRequest()->getParam('mime'));
		// Set the header
		$this->setHeaderContentType($sMimeType);
		// Display the blob
		echo FramsieCompression::getInstance()->decompressEntity($this->getRequest()->getParam('blob'));
		// We're done
		exit;
	}

	/**
	 * This method renders an encoded image url
	 * @package Framsie
	 * @subpackage AssetsController
	 * @access public
	 * @return void
	 */
	public function imageView() {
		// Disable the layout
		$this->setDisableLayout();
		// Disable the view
		$this->getView()->setDisableView();
		// Decode the file
		$sImage = base64_decode($this->getRequest()->getParam('file'));
		// Determine the header type
		switch (preg_replace('/^.*\.([^.]+)$/D', '$1', $sImage)) {
			// EOT Font
			case 'eot'  : $this->setHeaderContentType(self::FONT);  break;
			// GIF
			case 'gif'  : $this->setHeaderContentType(self::IMG_GIF);   break;
			// JPEG
			case 'jpeg' : $this->setHeaderContentType(self::IMG_JPEG);  break;
			// JPG
			case 'jpg'  : $this->setHeaderContentType(self::IMG_JPEG);  break;
			// OTF Font
			case 'otf'  : $this->setHeaderContentType(self::FONT);  break;
			// PNG
			case 'png'  : $this->setHeaderContentType(self::IMG_PNG);   break;
			// SVG
			case 'svg'  : $this->setHeaderContentType(self::IMG_SVG);   break;
			// SVGZ
			case 'svgc' : $this->setHeaderContentType(self::IMG_SVG);   break;
			// TIFF
			case 'tif'  : $this->setHeaderContentType(self::IMG_TIF);   break;
			// TTF Font
			case 'ttf'  : $this->setHeaderContentType(self::FONT);  break;
			// WOFF Font
			case 'woff' : $this->setHeaderContentType(self::FONT); break;
		}
		// Print the image
		readfile(BLOCK_PATH.'/'.IMG_ASSETS_PATH."/{$sImage}");
		// Terminate
		exit;
	}

	/**
	 * This view method renders a script and has the capability to minify it as well
	 * @package Framsie
	 * @subpackage AssetsController
	 * @access public
	 * @return void
	 */
	public function scriptView() {
		// Set the header content
		// $this->setHeaderContentType(self::HEADER_JAVASCRIPT);
		// Disable the layout
		$this->setDisableLayout();
		// Decode the file
		$sScript = (string) base64_decode($this->getRequest()->getParam('file'));
		// Determine if this is a compressed array
		if ($this->getRequest()->getParam('compressed') === true) {
			// Decompress and deserialize the scripts
			$sScript = FramsieCompression::getInstance()->decompressEntity($sScript);
		}
		// Check to see if we need to minify the source
		if ($this->getRequest()->getParam('minify') !== false) { // The source should be minified
			// Get the source
			$sSource = (string) FramsieAssets::getInstance()->getJavascript($sScript, true);
		} else {                                                  // The source should not be minified
			// Get the source
			$sSource = (string) FramsieAssets::getInstance()->getJavascript($sScript, false);
		}
		// Set the script source
		$this->mView->sScriptSource = $sSource;
	}

	/**
	 * This view method renders a stylesheet and has the capability to minify it as well
	 * @package Framsie
	 * @subpackage AssetsController
	 * @access public
	 * @return void
	 */
	public function styleView() {
		// Set the header content
		$this->setHeaderContentType(self::HEADER_CSS);
		// Disable the layout
		$this->setDisableLayout();
		// Decode the file
		$sStyle = (string) base64_decode($this->getRequest()->getParam('file'));
		// Determine if this is a compressed array
		if ($this->getRequest()->getParam('compressed') === true) {
			// Decompress and deserialize the styles
			$sStyle = FramsieCompression::getInstance()->decompressEntity($sStyle);
		}
		// Check to see if we need to minify the source
		if ($this->getRequest()->getParam('minify') !== false) { // The source should be minified
			// Get the source
			$sSource = (string) FramsieAssets::getInstance()->getCss($sStyle, true);
		} else {                                                 // The source should not be minified
			// Get the source
			$sSource = (string) FramsieAssets::getInstance()->getCss($sStyle, false);
		}
		// Set the style source
		$this->mView->sStyleSource = $sSource;
	}
}
