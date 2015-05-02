<?php
/*
Plugin Name: Layotter
Description: Add and arrange your content freely with an intuitive drag and drop interface!
Author: Dennis Hingst
Version: 1.2.1
Author URI: http://www.layotter.com/
Text Domain: layotter
*/


// settings are self-contained and should be included even if ACF is not available
// one reason is that otherwise default settings would not be registered on plugin activation
require_once __DIR__ . '/core/settings.php';


// check if ACF is installed and the version is compatible
require_once __DIR__ . '/core/check-acf.php';


// include files only if ACF is available
if (!defined('LAYOTTER_ACF_ERROR')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    require_once __DIR__ . '/core/core.php';
    require_once __DIR__ . '/core/ajax.php';
    require_once __DIR__ . '/core/assets.php';
    require_once __DIR__ . '/core/interface.php';
    require_once __DIR__ . '/core/templates.php';
    require_once __DIR__ . '/core/layouts.php';
    require_once __DIR__ . '/core/acf-locations.php';
    require_once __DIR__ . '/core/shortcode.php';
    require_once __DIR__ . '/core/views.php';

    require_once __DIR__ . '/components/form.php';
    require_once __DIR__ . '/components/editable.php';
    require_once __DIR__ . '/components/options.php';
    require_once __DIR__ . '/components/post.php';
    require_once __DIR__ . '/components/row.php';
    require_once __DIR__ . '/components/col.php';
    require_once __DIR__ . '/components/element.php';
}


class ly_ex extends Layotter_Element {
    public function attributes() {
        $this->title = 'Example element';
        $this->description = 'Desc.';
        $this->icon = 'file';
        $this->field_group = 8;
    }

    public function frontend_view($fields) {
        print_r($fields);
    }

    public function backend_view($fields) {
        print_r($fields);
    }
}
Layotter::register_element('example', 'ly_ex');