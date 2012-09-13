<?php
// Load the bootstrapper
require_once(dirname(__FILE__).'/../lib/Bootstrap.php');
// Define the environment
define('APPLICATION_ENVIRONMENT', 'development');
// Instantiate the Bootstrapper
Bootstrap::getInstance();
// Dispatch the system
Bootstrap::getInstance()->dispatch();
