<?php
/*
Plugin Name: Layotter
Description: Add and arrange your content freely with an intuitive drag and drop editor!
Author: Dennis Hingst
Version: 2.0.0
Author URI: http://www.layotter.com/
Text Domain: layotter
GitHub Plugin URI: hingst/layotter
*/

namespace Layotter;

spl_autoload_register(function($class) {
    if (stripos($class, __NAMESPACE__ . '\\Tests\\') === 0) {
        require_once __DIR__ . '/tests/core' . str_replace('\\', '/', substr($class, strlen(__NAMESPACE__ . '\\Tests'))) . '.php';
    } else if (stripos($class, __NAMESPACE__ . '\\') === 0) {
        require_once __DIR__ . '/core' . str_replace('\\', '/', substr($class, strlen(__NAMESPACE__))) . '.php';
    }
});

Initializer::run();
