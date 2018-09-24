<?php

/**
 * The easiest way to run this test suite is using the Layotter development
 * environment from https://github.com/hingst/layotter-dev-env
 */

// configuration for unit tests
define('TESTS_FRAMEWORK_PATH', getenv('TESTS_WP_FRAMEWORK_PATH')); // path to the testing framework
define('TESTS_ACF_PATH', getenv('TESTS_ACF_PATH')); // path to ACF files

// configuration for Selenium tests
define('TESTS_WP_HOST', getenv('TESTS_WP_HOST')); // URL to Wordpress installation, e.g. http://wordpress.dev
define('TESTS_WP_USER', getenv('TESTS_WP_USER')); // Wordpress admin user name
define('TESTS_WP_PASSWORD', getenv('TESTS_WP_PASSWORD')); // Wordpress admin password
define('TESTS_SELENIUM_HOST', getenv('TESTS_SELENIUM_HOST')); // URL to Selenium server, e.g. http://selenium.dev:4444
define('TESTS_SELENIUM_BROWSER', getenv('TESTS_SELENIUM_BROWSER')); // Selenium browser engine, e.g. chrome

// include Composer dependencies and WP testing framework
$tests_dir = '/tmp/wordpress-tests-lib';
require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once TESTS_FRAMEWORK_PATH . '/includes/functions.php';

// initialize after plugins have been loaded
tests_add_filter('muplugins_loaded', '_manually_load_plugin');
function _manually_load_plugin() {

    // include ACF
    require TESTS_ACF_PATH . '/acf.php';

    // include and initialize Layotter
    require dirname(dirname(__FILE__)) . '/index.php';
    \Layotter\Settings::set_defaults_on_activation();

    // include helpers
    require dirname(__FILE__) . '/include/field-groups.php';
    require dirname(__FILE__) . '/include/data.php';
    require dirname(__FILE__) . '/include/filters.php';
}

// start up the WP testing framework
require TESTS_FRAMEWORK_PATH . '/includes/bootstrap.php';
