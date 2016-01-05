<?php

require __DIR__ . '/../vendor/autoload.php';

//Load our environment
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

use Phalcon\Mvc\Application;

error_reporting(E_ALL);

define('APP_PATH', realpath(__DIR__ . '/../'));

/**
 * Read the configuration
 */
$config = include APP_PATH . "/config/config.php";

/**
 * Include services
 */
require __DIR__ . '/../config/services.php';

/**
 * Set up the application
 */
$application = new Application($di);

return $application;