<?php
/*

        __                      __  __
       / /   ____ ___  ______  / /_/ /____  _____
      / /   / __ `/ / / / __ \/ __/ __/ _ \/ ___/
     / /___/ /_/ / /_/ / /_/ / /_/ /_/  __/ /
    /_____/\__,_/\__, /\____/\__/\__/\___/_/
                /____/


Plugin Name: Layotter
Description: Add and arrange your content freely with an intuitive drag and drop interface!
Author: Dennis Hingst
Version: 2.0.0
Author URI: http://www.layotter.com/
Text Domain: layotter
GitHub Plugin URI: hingst/layotter
*/

namespace Layotter;

spl_autoload_register(function($class) {
    if (stripos($class, __NAMESPACE__ . '\\') === 0) {
        require_once __DIR__ . '/core' . str_replace('\\', '/', substr($class, strlen(__NAMESPACE__))) . '.php';
    }
});

Core::init();
