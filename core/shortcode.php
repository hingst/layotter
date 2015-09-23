<?php


/**
 * Add shortcode for frontend output
 */
add_shortcode('layotter', 'layotter_frontend_shortcode');


/**
 * Process post content
 *
 * @param array $atts Shortcode attributes
 * @param string $input Data wrapped by the [layotter] shortcode
 * @return string HTML for frontend view of the current post
 */
function layotter_frontend_shortcode($atts, $input = '') {
    // since 1.5.0, shortcode attributes carry a post ID, and JSON is stored in a custom field
    // before 1.5.0, JSON was stored directly in the post content
    // get_the_ID() wouldn't be reliable here because this shortcode handler might be triggered in a context where the
    // $post variable hasn't been correctly initialized, like do_shortcode() or apply_filters('the_content')
    if (isset($atts['post']) AND Layotter::is_enabled_for_post($atts['post'])) {
        $post_id = intval($atts['post']);
        $layotter = new Layotter_Post($post_id);
    } else {
        $layotter = new Layotter_Post($input);
    }

    return $layotter->get_frontend_view();
}