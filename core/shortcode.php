<?php


/**
 * Add shortcode for frontend output
 *
 * Layotter uses the shortcode [layotter] containing the JSON-encoded content
 * structure of a given post.
 */
add_shortcode('layotter', 'layotter_frontend_shortcode');


/**
 * Process post content
 *
 * @param array $atts Required by Wordpress, but unused
 * @param string $input Data wrapped by the [layotter] shortcode
 * @return string HTML for frontend view of the current post
 */
function layotter_frontend_shortcode($atts, $input = '') {
    // ignore $input and extract JSON data ourself, because 'the_content' filters might have invalidated JSON
    $layotter = new Layotter_Post(get_the_ID());

    // turn content structure into HTML and return
    return $layotter->get_frontend_view();
}