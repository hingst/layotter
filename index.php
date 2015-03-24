<?php
/*
Plugin Name: Eddditor
Description: Add and arrange your content freely with an intuitive drag and drop interface!
Author: Dennis Hingst
Version: 1.1.1
Author URI: http://www.dennis-hingst.de/
Text Domain: eddditor
*/


// settings are self-contained and should be included even if ACF is not available
// one reason is that otherwise default settings would not be registered on plugin activation
require_once dirname(__FILE__) . '/settings.php';


// check if ACF is installed and the version is compatible
require_once dirname(__FILE__) . '/check-acf.php';


// include files only if ACF is available
if (!defined('EDDDITOR_ACF_ERROR')) {
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

    require_once dirname(__FILE__) . '/components/elements/test.php';
}





// ******** debug and testing below this line **********************************************









//update_option('eddditor_element_templates', array());
//update_option('eddditor_post_layouts', array());





// EXAMPLE FILTERS
// 
add_filter('eddditor/post', 'eddditor_filter_post', 10, 2);
function eddditor_filter_post($content, $options)
{
    ob_start();
    var_dump($options);
    $op = ob_get_clean();

    return $op . '<div class="post">' . $content . '</div>';
}
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