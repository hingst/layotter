<?php


/**
 * Load translation files
 *
 * No need to hook this to plugins_loaded because this file is included at that time anyway.
 */
load_plugin_textdomain('layotter', false, basename(dirname(__DIR__)) . '/languages/');


/**
 * Replace TinyMCE with Layotter on Layotter-enabled screens
 */
add_action('admin_head', 'layotter_admin_head');
function layotter_admin_head() {
    if (!Layotter::is_enabled()) {
        return;
    }

    $post_type = get_post_type();

    // remove TinyMCE
    remove_post_type_support($post_type, 'editor');

    // insert layotter
    add_meta_box(
        'layotter_wrapper', // ID
        'Layotter', // title
        'layotter_output_interface', // callback
        $post_type, // post type for which to enable
        'normal', // position
        'high' // priority
    );
}


/**
 * Output backend HTML for Layotter
 *
 * @param $post object Post object as provided by Wordpress
 */
function layotter_output_interface($post) {
    // prepare JSON data for representation in textarea
    $content = get_field('layotter_post_content', $post->ID);
    $clean_content_for_textarea = htmlspecialchars($content);

    echo '<textarea id="content" name="content" style="width: 1px; height: 1px; position: fixed; top: -999px; left: -999px;">' . $clean_content_for_textarea . '</textarea>';
    
    require_once __DIR__ . '/../views/editor.php';
}