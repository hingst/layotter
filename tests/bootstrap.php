<?php

$_tests_dir = getenv('WP_TESTS_DIR');
if (!$_tests_dir) {
    $_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
    require dirname(dirname(dirname(__FILE__))) . '/advanced-custom-fields/acf.php';
    require dirname(dirname(__FILE__)) . '/index.php';
    \Layotter\Settings::set_defaults_on_activation();
    require dirname(__FILE__) . '/helpers/field-group.php';
    require dirname(__FILE__) . '/helpers/data.php';
    require dirname(__FILE__) . '/helpers/element.php';
}

tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
