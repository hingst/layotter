<?php
/*
Plugin Name: Layotter
Description: Add and arrange your content freely with an intuitive drag and drop interface!
Author: Dennis Hingst
Version: 1.2.0
Author URI: http://www.layotter.com/
Text Domain: layotter
*/


// settings are self-contained and should be included even if ACF is not available
// one reason is that otherwise default settings would not be registered on plugin activation
require_once dirname(__FILE__) . '/settings.php';


// check if ACF is installed and the version is compatible
require_once dirname(__FILE__) . '/check-acf.php';


// include files only if ACF is available
if (!defined('LAYOTTER_ACF_ERROR')) {
    require_once dirname(__FILE__) . '/core.php';
    require_once dirname(__FILE__) . '/ajax.php';
    require_once dirname(__FILE__) . '/assets.php';
    require_once dirname(__FILE__) . '/interface.php';
    require_once dirname(__FILE__) . '/templates.php';
    require_once dirname(__FILE__) . '/layouts.php';
    require_once dirname(__FILE__) . '/acf-locations.php';
    require_once dirname(__FILE__) . '/shortcode.php';

    require_once dirname(__FILE__) . '/components/form.php';
    require_once dirname(__FILE__) . '/components/editable.php';
    require_once dirname(__FILE__) . '/components/options.php';
    require_once dirname(__FILE__) . '/components/post.php';
    require_once dirname(__FILE__) . '/components/row.php';
    require_once dirname(__FILE__) . '/components/col.php';
    require_once dirname(__FILE__) . '/components/element.php';
}