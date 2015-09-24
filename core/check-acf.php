<?php


define('LAYOTTER_ACF_VERSION_REQUIRED', '5.2.6');


// check if ACF is installed and the version is compatible with this version of Layotter
if (!class_exists('acf')) {
    // error: ACF isn't installed
    define('LAYOTTER_ACF_ERROR', sprintf(__('Layotter requires the <a href="%s" target="_blank">Advanced Custom Fields Pro</a> plugin, please install it before using Layotter.', 'layotter'), 'http://www.advancedcustomfields.com'));
} else if (!class_exists('acf_pro')) {
    // error: ACF isn't installed in the Pro version (currently only ACF Pro is supported)
    //define('LAYOTTER_ACF_ERROR', __('Layotter currently only works with the Pro version of Advanced Custom Fields. Please install it before using Layotter. We apologize for the inconvenience.', 'layotter'));
} else if (!function_exists('acf_get_setting') OR version_compare(acf_get_setting('version'), LAYOTTER_ACF_VERSION_REQUIRED) < 0) {
    // error: ACF version is outdated
    define('LAYOTTER_ACF_ERROR', sprintf(__('Your version of Advanced Custom Fields is outdated. Please install version %s or higher to be able to use Layotter.', 'layotter'), LAYOTTER_ACF_VERSION_REQUIRED));
}


// display an error message in the backend if there's a problem with ACF
if (defined('LAYOTTER_ACF_ERROR')) {
    add_action('admin_notices', 'layotter_acf_warning');
}
function layotter_acf_warning() {
    ?>
    <div class="error">
        <p>
            <?php echo LAYOTTER_ACF_ERROR; ?>
        </p>
    </div>
    <?php
}