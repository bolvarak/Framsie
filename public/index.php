<?php
// Load the bootstrapper
require_once(dirname(__FILE__).'/../lib/Bootstrap.php');
// Define the environment
define('APPLICATION_ENVIRONMENT', 'development');
// Instantiate the Bootstrapper
Bootstrap::getInstance();
/**
 * Redirects Go Here
 */
// Framsie::getInstance()->addRedirect('/foo/bar', '/home/default'); // Redirect the foo/bar page to the home page
// Dispatch the system
Bootstrap::getInstance()->dispatch();
