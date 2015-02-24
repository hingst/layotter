<?php
/*
Plugin Name: Eddditor with AngularJS
Description: Add and arrange your content freely with an intuitive drag and drop interface!
Author: Dennis Hingst
Version: 1.0
Author URI: http://www.dennis-hingst.de/
Text Domain: eddditor
*/


// include Advanced Custom Fields if it isn't already installed
if(!class_exists('acf') AND file_exists(dirname(__FILE__) . '/acf/acf.php'))
{
    define('ACF_LITE', true); // don't show in admin menu
    require_once dirname(__FILE__) . '/acf/acf.php';
}


require_once dirname(__FILE__) . '/core.php';

require_once dirname(__FILE__) . '/element.php';
require_once dirname(__FILE__) . '/elements/_example.php';

require_once dirname(__FILE__) . '/form.php';
require_once dirname(__FILE__) . '/options.php';
require_once dirname(__FILE__) . '/ajax.php';
require_once dirname(__FILE__) . '/assets.php';
require_once dirname(__FILE__) . '/interface.php';
require_once dirname(__FILE__) . '/acf-locations.php';
require_once dirname(__FILE__) . '/frontend.php';
require_once dirname(__FILE__) . '/settings.php';
require_once dirname(__FILE__) . '/templates.php';




//update_option('eddditor_element_templates', array());










// EXAMPLE FILTERS
// 
//add_filter('eddditor/post', 'eddditor_filter_post', 10, 2);
//function eddditor_filter_post($content, $options)
//{
//    ob_start();
//    var_dump($options);
//    $op = ob_get_clean();
//    
//    return $op . '<div class="post">' . $content . '</div>';
//}
//
//add_filter('eddditor/row', 'eddditor_filter_row', 10, 2);
//function eddditor_filter_row($content, $options)
//{
//    return '<div class="row">' . $content . '</div>';
//}
//
//add_filter('eddditor/col', 'eddditor_filter_col', 10, 2);
//function eddditor_filter_col($content, $class)
//{
//    return '<div class="' . $class . '">' . $content . '</div>';
//}
//
/*
add_filter('eddditor/element', 'eddditor_filter_element', 10, 2);
function eddditor_filter_element($content, $options)
{
    ob_start();
    var_dump($options);
    $opts = ob_get_clean();
    
    return $opts . '<div class="element">' . $content . '</div>';
}
*/