<?php


/**
 * Add shortcode for frontend output
 *
 * Eddditor uses the shortcode [eddditor] containing the JSON-encoded content
 * structure of a given post.
 */
add_shortcode('eddditor', 'eddditor_frontend_shortcode');


/**
 * Process post content
 *
 * @param array $atts Required by Wordpress, but unused
 * @param string $input Data wrapped by the [eddditor] shortcode
 * @return string HTML for frontend view of the current post
 */
function eddditor_frontend_shortcode($atts, $input = '') {
    // ignore $input and extract JSON data ourself, because 'the_content' filters might have invalidated JSON
    $eddditor = new Eddditor_Post(get_the_ID());

    // turn content structure into HTML and return
    return $eddditor->get_frontend_view();
}