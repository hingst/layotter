<?php

/**
 * The easiest way to run this test suite is using the Layotter development
 * environment from https://github.com/hingst/layotter-dev-env
 */

if (!defined('LAYOTTER_PHPUNIT_TESTING')) {
    return;
}

// configuration for unit tests
define('TESTS_WP_PATH', getenv('TESTS_WP_PATH')); // path to Wordpress files

// configuration for Selenium tests
define('TESTS_WP_HOST', getenv('TESTS_WP_HOST')); // URL to Wordpress installation, e.g. http://wordpress.dev
define('TESTS_WP_USER', getenv('TESTS_WP_USER')); // Wordpress admin user name
define('TESTS_WP_PASSWORD', getenv('TESTS_WP_PASSWORD')); // Wordpress admin password
define('TESTS_SELENIUM_HOST', getenv('TESTS_SELENIUM_HOST')); // URL to Selenium server, e.g. http://selenium.dev:4444
define('TESTS_SELENIUM_BROWSER', getenv('TESTS_SELENIUM_BROWSER')); // Selenium browser engine, e.g. chrome

// database credentials
define('TESTS_WP_DB_NAME', getenv('TESTS_WP_DB_NAME'));
define('TESTS_WP_DB_HOST', getenv('TESTS_WP_DB_HOST'));
define('TESTS_WP_DB_USER', getenv('TESTS_WP_DB_USER'));
define('TESTS_WP_DB_PASSWORD', getenv('TESTS_WP_DB_PASSWORD'));

// file name in wp-uploads directory
define('TESTS_UPLOAD_FILE_NAME', getenv('TESTS_UPLOAD_FILE_NAME'));

// include Composer dependencies and WP testing framework
require dirname(__FILE__) . '/vendor/autoload.php';
require dirname(__FILE__) . '/vendor/lipemat/wp-unit/includes/functions.php';

// start up the WP testing framework
require dirname(__FILE__) . '/wp-tests-config.php';
require dirname(__FILE__) . '/vendor/lipemat/wp-unit/includes/bootstrap-no-install.php';
