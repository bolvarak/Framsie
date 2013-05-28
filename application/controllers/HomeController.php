<?php

class HomeController extends FramsieController {

	/**
	 * This view method is simply a default Framsie example
	 * @package Framsie
	 * @subpackage HomeController
	 * @access public
	 * @return void
	 */
	public function defaultView() {
		// Set the page title
		$this->setPageTitle('Framsie Default Page');
	}
}