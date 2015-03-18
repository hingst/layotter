<?php


/**
 * Decode POST data sent via Angular's $http service
 *
 * Due to the way Angular encodes POST data, PHP will not populate $_POST
 * automatically.
 *
 * @return array Post data
 */
function eddditor_get_angular_post_data() {
    return (array) json_decode(file_get_contents('php://input'), true);
}






/**
 * Output the edit form for an element
 */
add_action('wp_ajax_eddditor_edit_element', 'eddditor_ajax_edit_element');
function eddditor_ajax_edit_element() {
    $post_data = eddditor_get_angular_post_data();
    
    // type is required
    if (isset($post_data['type']) AND is_string($post_data['type'])) {
        if (isset($post_data['values'])) {
            $values = $post_data['values'];
        } else {
            $values = array();
        }

        $element = Eddditor::create_element($post_data['type'], $values);
        if ($element) {
            $element->output_form();
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Output JSON-encoded element data (called after editing an element)
 */
add_action('wp_ajax_eddditor_parse_element', 'eddditor_ajax_parse_element');
function eddditor_ajax_parse_element() {
    $post_data = eddditor_get_angular_post_data();
    
    // type and field values are required
    if (isset($post_data['type']) AND is_string($post_data['type']) AND isset($post_data['values']['acf']) AND is_array($post_data['values']['acf'])) {
        // ACF compatibility: unwrap field names from acf[...]
        // the acf[...] wrapper is required by acf's validation mechanism
        $values = $post_data['values']['acf'];

        $element = Eddditor::create_element($post_data['type'], $values);
        if ($element) {
            echo json_encode($element);
        }
    }

    die(); // required by Wordpress after any AJAX call
}






/**
 * Output the edit form for post, row or element options
 */
add_action('wp_ajax_eddditor_edit_options', 'eddditor_ajax_edit_options');
function eddditor_ajax_edit_options() {
    $post_data = eddditor_get_angular_post_data();

    // type and option values are required
    if (isset($post_data['type']) AND is_string($post_data['type'])) {
        if (isset($post_data['values'])) {
            $values = $post_data['values'];
        } else {
            $values = array();
        }

        $options = new Eddditor_Options($post_data['type'], $values);
        if ($options->is_enabled()) {
            $options->output_form();
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Output JSON-encoded options data (called after editing post, row or element options)
 */
add_action('wp_ajax_eddditor_parse_options', 'eddditor_ajax_parse_options');
function eddditor_ajax_parse_options() {
    $post_data = eddditor_get_angular_post_data();
    
    // type and option values are required
    if (isset($post_data['type']) AND is_string($post_data['type']) AND isset($post_data['values']['acf']) AND is_array($post_data['values']['acf'])) {
        // ACF compatibility: unwrap field names from acf[...]
        // the acf[...] wrapper is required by acf's validation mechanism
        $values = $post_data['values']['acf'];

        $options = new Eddditor_Options($post_data['type'], $values);
        if($options->is_enabled()) {
            echo json_encode($options);
        }
    }

    die(); // required by Wordpress after any AJAX call
}