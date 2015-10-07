<?php


// check if ACF is installed and the version is compatible with this version of Layotter
if (!Layotter_ACF::is_installed()) {
    // error: ACF isn't installed
    define('LAYOTTER_ACF_ERROR', sprintf(__('Layotter requires the <a href="%s" target="_blank">Advanced Custom Fields Pro</a> plugin, please install it before using Layotter.', 'layotter'), 'http://www.advancedcustomfields.com'));
} else if (!Layotter_ACF::is_pro_installed()) {
    // error: ACF isn't installed in the Pro version (currently only ACF Pro is supported)
    define('LAYOTTER_ACF_ERROR', sprintf(__('Layotter currently only works with the Pro version of <a href="%s" target="_blank">Advanced Custom Fields</a>. Please install it before using Layotter. <a href="%s" target="_blank">Why?</a>', 'layotter'), 'http://www.advancedcustomfields.com', 'http://docs.layotter.com/getting-started/installation/#requirements'));
} else if (!Layotter_ACF::is_version_compatible()) {
    // error: ACF version is outdated
    define('LAYOTTER_ACF_ERROR', sprintf(__('Your version of Advanced Custom Fields is outdated. Please install version %s or higher to be able to use Layotter.', 'layotter'), Layotter_ACF::REQUIRED_VERSION));
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