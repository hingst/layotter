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

        if (isset($post_data['post_id'])) {
            $post_id = $post_data['post_id'];
        } else {
            $post_id = '';
        }

        $options = new Eddditor_Options($post_data['type'], $values, $post_id);
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

        if (isset($post_data['post_id'])) {
            $post_id = $post_data['post_id'];
        } else {
            $post_id = '';
        }

        $options = new Eddditor_Options($post_data['type'], $values, $post_id);
        if($options->is_enabled()) {
            echo json_encode($options);
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
    if (isset($post_data['type']) AND is_string($post_data['type'])) {
        if (isset($post_data['values'])) {
            $values = $post_data['values'];
        } else {
            $values = array();
        }

        $element = Eddditor::create_element($post_data['type'], $values);
        if ($element) {
            $template = Eddditor_Templates::save($element);
            echo json_encode($template);
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
    if (isset($post_data['template_id']) AND is_int($post_data['template_id'])) {
        $element = Eddditor_Templates::create_element($post_data['template_id']);
        if ($element) {
            $element->output_form();
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
    if (isset($post_data['template_id']) AND is_int($post_data['template_id']) AND isset($post_data['values']['acf']) AND is_array($post_data['values']['acf'])) {
        $id = $post_data['template_id'];
        $template = Eddditor_Templates::get($id);

        if ($template) {
            // acf compatibility: unwrap field names from acf[...]
            // the acf[...] wrapper is required by acf's validation mechanism
            $values = $post_data['values']['acf'];

            $element = Eddditor::create_element($template['type'], $values);
            if ($element) {
                $element->set_template_id($id);
                Eddditor_Templates::update($id, $element->get_template_data());
                echo json_encode($element);
            }
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
    if (isset($post_data['template_id'])) {
        $template_object = Eddditor_Templates::create_element($post_data['template_id']);
        if ($template_object) {
            Eddditor_Templates::delete($post_data['template_id']);
            $template_object->unset_template_id();
            echo json_encode($template_object);
        }
    }

    die(); // required by Wordpress after any AJAX call
}






/**
 * Save JSON structure as a post layout
 */
add_action('wp_ajax_eddditor_save_new_layout', 'eddditor_ajax_save_new_layout');
function eddditor_ajax_save_new_layout() {
    $post_data = eddditor_get_angular_post_data();

    // name and JSON are required
    if (isset($post_data['name']) AND isset($post_data['json'])) {
        $layout = Eddditor_Layouts::save($post_data['name'], $post_data['json']);
        echo json_encode($layout);
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Load a post layout
 */
add_action('wp_ajax_eddditor_load_layout', 'eddditor_ajax_load_layout');
function eddditor_ajax_load_layout() {
    $post_data = eddditor_get_angular_post_data();

    // template ID is required
    if (isset($post_data['layout_id'])) {
        $post = Eddditor_Layouts::get($post_data['layout_id']);
        if ($post) {
            echo json_encode($post);
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Rename a post layout
 */
add_action('wp_ajax_eddditor_rename_layout', 'eddditor_ajax_rename_layout');
function eddditor_ajax_rename_layout() {
    $post_data = eddditor_get_angular_post_data();

    // template ID and new name are required
    if (isset($post_data['layout_id']) AND isset($post_data['name'])) {
        $renamed = Eddditor_Layouts::rename($post_data['layout_id'], $post_data['name']);
        if ($renamed) {
            echo json_encode($renamed);
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Delete a post layout
 */
add_action('wp_ajax_eddditor_delete_layout', 'eddditor_ajax_delete_layout');
function eddditor_ajax_delete_layout() {
    $post_data = eddditor_get_angular_post_data();

    // template ID is required
    if (isset($post_data['layout_id'])) {
        Eddditor_Layouts::delete($post_data['layout_id']);
    }

    die(); // required by Wordpress after any AJAX call
}