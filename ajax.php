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
        $values
            = (isset($post_data['values']) AND is_array($post_data['values']))
            ? $post_data['values']
            : false; // => use default field values, should only happen when editing a new element

        $element = Eddditor::create_element($post_data['type'], $values);
        if ($element) {
            $form = $element->get('form');
            $form->output();
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
        // acf compatibility: unwrap field names from acf[...]
        // the acf[...] wrapper is required by acf's validation mechanism
        $values = $post_data['values']['acf'];

        $element = Eddditor::create_element($post_data['type'], $values);
        if ($element) {
            echo json_encode($element->get('data'));
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
    if (isset($post_data['type']) AND is_string($post_data['type']) AND isset($post_data['values']) AND is_array($post_data['values'])) {
        $options = new Eddditor_Options($post_data['type'], $post_data['values']);
        if ($options->is_enabled()) {
            $form = $options->get('form');
            $form->output();
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
        // acf compatibility: unwrap field names from acf[...]
        // the acf[...] wrapper is required by acf's validation mechanism
        $values = $post_data['values']['acf'];

        $options = new Eddditor_Options($post_data['type'], $values);
        if($options->is_enabled()) {
            echo json_encode($options->get('data'));
        }
    }

    die(); // required by Wordpress after any AJAX call
}






/**
 * Save element as a new template and output the new template's JSON-encoded data
 */
add_action('wp_ajax_eddditor_save_new_template', 'eddditor_ajax_save_new_template');
function eddditor_ajax_save_new_template() {
    $post_data = eddditor_get_angular_post_data();
    
    // type and field values are required
    if (isset($post_data['type']) AND is_string($post_data['type']) AND isset($post_data['values']) AND is_array($post_data['values'])) {
        $element = Eddditor::create_element($post_data['type'], $post_data['values']);
        if ($element) {
            $id = Eddditor_Templates::save($element->get('template_data'));
            $element->set_template($id);
            echo json_encode($element->get('data'));
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Update element template and output the template's JSON-encoded data
 */
add_action('wp_ajax_eddditor_update_template', 'eddditor_ajax_update_template');
function eddditor_ajax_update_template() {
    $post_data = eddditor_get_angular_post_data();
    
    // type and field values are required
    if (isset($post_data['template']) AND is_int($post_data['template']) AND isset($post_data['values']['acf']) AND is_array($post_data['values']['acf'])) {
        $id = $post_data['template'];
        $template = Eddditor_Templates::get($id);

        if ($template) {
            // acf compatibility: unwrap field names from acf[...]
            // the acf[...] wrapper is required by acf's validation mechanism
            $values = $post_data['values']['acf'];

            $element = Eddditor::create_element($template['type'], $values);
            if ($element) {
                Eddditor_Templates::update($id, $element->get('template_data'));
                $element->set_template($id);
                echo json_encode($element->get('data'));
            }
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Output the edit form for a template
 */
add_action('wp_ajax_eddditor_edit_template', 'eddditor_ajax_edit_template');
function eddditor_ajax_edit_template() {
    $post_data = eddditor_get_angular_post_data();

    // template ID is required
    if (isset($post_data['template']) AND is_int($post_data['template'])) {
        $element = Eddditor_Templates::create_element($post_data['template']);
        if ($element) {
            $form = $element->get('form');
            $form->output();
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Delete a template
 */
add_action('wp_ajax_eddditor_delete_template', 'eddditor_ajax_delete_template');
function eddditor_ajax_delete_template() {
    $post_data = eddditor_get_angular_post_data();

    // template ID is required
    if (isset($post_data['template']) AND is_int($post_data['template'])) {
        Eddditor_Templates::delete($post_data['template']);
    }

    die(); // required by Wordpress after any AJAX call
}