<?php


// check if ACF is installed and the version is compatible with this version of Eddditor
if (!class_exists('acf')) {
    // error: ACF isn't installed
    define('EDDDITOR_ACF_ERROR', sprintf(__('Eddditor requires the <a href="%s" target="_blank">Advanced Custom Fields Pro</a> plugin, please install it before using Eddditor.', 'eddditor'), 'http://www.advancedcustomfields.com'));
} else if (!class_exists('acf_pro')) {
    // error: ACF isn't installed in the Pro version (currently only ACF Pro is supported)
    define('EDDDITOR_ACF_ERROR', __('Eddditor currently only works with the Pro version of Advanced Custom Fields. Please install it before using Eddditor. We apologize for the inconvenience.', 'eddditor'));
} else if (!function_exists('acf_get_setting') OR version_compare(acf_get_setting('version'), EDDDITOR_ACF_VERSION_REQUIRED) < 0) {
    // error: ACF version is outdated
    define('EDDDITOR_ACF_ERROR', sprintf(__('Your version of Advanced Custom Fields is outdated. Please install version %s or higher to be able to use Eddditor.', 'eddditor'), EDDDITOR_ACF_VERSION_REQUIRED));
}


// display an error message in the backend if there's a problem with ACF
add_action('admin_notices', 'eddditor_acf_warning');
function eddditor_acf_warning() {
    if (defined('EDDDITOR_ACF_ERROR')) {
        ?>
        <div class="error">
            <p>
                <?php echo EDDDITOR_ACF_ERROR; ?>
            </p>
        </div>
    <?php
    }
}