<?php
// Load the bootstrapper
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Framsie.php');
// Initialize the system
Framsie::Bootstrap();
// Initialize the error class
FramsieError::InitializeErrorsFromDatabase('ErrorLookup', 'Code', 'Message');
// Add a redirect
Framsie::getInstance()->addRedirect('/home/faq', '/home/faqs');
// Initialize Caching
FramsieCache::getInstance()->setExpire(1);
// Dispatch the system
Framsie::getInstance()                         // Instantiate the Bootstrapper
	->setEnvironment(Framsie::ENV_DEVELOPMENT) // Set the application environment
	->dispatch();                              // Dispatch Framsie
