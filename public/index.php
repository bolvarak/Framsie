<?php
// Load the bootstrapper
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Bootstrap.php');
// Define the environment
define('APPLICATION_ENVIRONMENT', 'development');
// Instantiate the Bootstrapper
Bootstrap::getInstance();
// Dispatch the system
Bootstrap::getInstance()->dispatch();
