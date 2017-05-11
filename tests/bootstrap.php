<?php
/**
 * Set error reporting and display errors settings.  You will want to change these when in production.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');

defined("DS") or define("DS", DIRECTORY_SEPARATOR);
defined("VENDOR") or define("VENDOR", __DIR__ . DS . "vendor");
/**
 * Composer autoloader
 */
function composer_autoload()
{
	// store the autoloader here
	static $composer;

	if(! $composer) {
		if ( ! is_file( VENDOR . DS . "autoload.php" ) )
		{
			die('Composer is not installed. Please run "php composer.phar update" to install Composer');
		}

		$composer = require(VENDOR . DS . "autoload.php");
	}

	return $composer;
}

$composer = composer_autoload();