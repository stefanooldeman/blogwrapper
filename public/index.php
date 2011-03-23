<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

//Define debug environment
define('DEBUG', true);

// Ensure library is on include_path
set_include_path(
	realpath(APPLICATION_PATH . '/../lib')
	. PATH_SEPARATOR . get_include_path()
);

/* Dwoo Templating solution */
require_once('Dwoo/lib/dwooAutoload.php');

/** Zend_Application */
require_once 'Zend/Application.php';

require 'markdown.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/config.ini');
$application->bootstrap()->run();
