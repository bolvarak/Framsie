<?php
// Load the bootstrapper
require_once(dirname(__FILE__).'/../lib/Bootstrap.php');
// Define the environment
define('APPLICATION_ENVIRONMENT', 'development');
// Dispatch the system
Bootstrap::getInstance()->dispatch();