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
		$this->setHeaderContentType(self::HEADER_JAVASCRIPT);
		// Disable the layout
		$this->setDisableLayout();
		// Decode the file
		$sScript = (string) base64_decode($this->getRequest()->getParam('file'));
		// Check to see if we need to minify the source
		if ($this->getRequest()->getParam('minify') === true) { // The source should be minified
			// Get the source
			$sSource = (string) FramsieAssets::getInstance()->getJavascript( // Instantiate the assets manager
				$sScript,                                                    // Send the block file
				"assets.script.{$this->getRequest()->getParam('file')}"      // Send the cache file name
			);
		} else {                                                  // The source should not be minified
			// Get the source
			$sSource = (string) FramsieAssets::getInstance()->getJavascript( // Instantiate the assets manager
					$sScript,                                                // Send the block file
					"assets.script.{$this->getRequest()->getParam('file')}", // Send the cache file name
					false                                                    // Do not minify the source
			);
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
		// Check to see if we need to minify the source
		if ($this->getRequest()->getParam('minify') === true) { // The source should be minified
			// Get the source
			$sSource = (string) self::getInstance()->getCss(           // Instantiate the assets manager
				$sStyle,                                               // Send the block file
				"assets.style.{$this->getRequest()->getParam('file')}" // Send the cache name
			);
		} else {                                                 // The source should not be minified
			// Get the source
			$sSource = (string) FramsieAssets::getInstance()->getCss(       // Instantiate the assets manager
					$sStyle,                                                // Send the block file
					"assets.style.{$this->getRequest()->getParam('file')}", // Send the cache name
					false                                                   // Do not minify the source
			);
		}
		// Set the style source
		$this->mView->sStyleSource = $sSource;
	}
}