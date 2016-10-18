<?php
/*
Plugin Name: Layotter
Description: Add and arrange your content freely with an intuitive drag and drop interface!
Author: Dennis Hingst
Version: 1.5.11
Author URI: http://www.layotter.com/
Text Domain: layotter
GitHub Plugin URI: hingst/layotter
*/


// load translations
load_plugin_textdomain('layotter', false, basename(__DIR__) . '/languages/');


// settings are self-contained and should be included even if ACF is not available
// one reason is that otherwise default settings would not be registered on plugin activation
require_once __DIR__ . '/core/settings.php';


// include other files after plugins are loaded so ACF checks can be run
add_action('plugins_loaded', 'layotter');
function layotter() {

    // ACF abstraction layer is always required
    require_once __DIR__ . '/core/acf-abstraction.php';

    // include files only if ACF is available
    if (Layotter_ACF::is_available()) {
        require_once __DIR__ . '/core/core.php';
        require_once __DIR__ . '/core/ajax.php';
        require_once __DIR__ . '/core/assets.php';
        require_once __DIR__ . '/core/interface.php';
        require_once __DIR__ . '/core/templates.php';
        require_once __DIR__ . '/core/layouts.php';
        require_once __DIR__ . '/core/acf-locations.php';
        require_once __DIR__ . '/core/shortcode.php';
        require_once __DIR__ . '/core/views.php';
        require_once __DIR__ . '/core/revisions.php';

        require_once __DIR__ . '/components/form.php';
        require_once __DIR__ . '/components/editable.php';
        require_once __DIR__ . '/components/options.php';
        require_once __DIR__ . '/components/post.php';
        require_once __DIR__ . '/components/row.php';
        require_once __DIR__ . '/components/col.php';
        require_once __DIR__ . '/components/element.php';

        // this library takes care of saving custom fields for each post revision
        // see https://wordpress.org/plugins/wp-post-meta-revisions/
        if (!class_exists('WP_Post_Meta_Revisioning')) {
            require_once __DIR__ . '/lib/wp-post-meta-revisions.php';
        }

        // include example element after theme is loaded (allows disabling the
        // example element with a settings filter in the theme)
        add_action('after_setup_theme', 'layotter_include_example_element');
    }
}

function layotter_include_example_element() {
    if (Layotter_Settings::example_element_enabled()) {
        require_once __DIR__ . '/example/field-group.php';
        require_once __DIR__ . '/example/element.php';
    }
}