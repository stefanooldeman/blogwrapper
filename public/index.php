<?php
putenv('APPLICATION_ENV=local');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

//Define debug environment
define('DEBUG', true);

// Ensure library is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../lib'),
    get_include_path(),
)));

/* Dwoo Templating solution */
require_once('dwooAutoload.php');

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/config.ini');
$application->bootstrap()->run();
