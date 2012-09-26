<?php
// Load the bootstrapper
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Bootstrap.php');
// Dispatch the system
Bootstrap::getInstance()                    // Instantiate the Bootstrapper
	->setEnvironment(Framsie::ENV_STAGING)  // Set the application environment
	->dispatch();                           // Dispatch Framsie
