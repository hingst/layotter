<?php
/*
Plugin Name: Layotter
Description: Add and arrange your content freely with an intuitive drag and drop interface!
Author: Dennis Hingst
Version: 1.5.15
Author URI: http://www.layotter.com/
Text Domain: layotter
GitHub Plugin URI: hingst/layotter
*/

namespace Layotter;

spl_autoload_register(function($class) {
    if (stripos($class, __NAMESPACE__) === 0) {
        require_once __DIR__ . '/core' . str_replace('\\', '/', strtolower(substr($class, strlen(__NAMESPACE__)))) . '.php';
    }
});

// load translations
load_plugin_textdomain('layotter', false, basename(__DIR__) . '/languages/');

Settings::init();
Core::init();
