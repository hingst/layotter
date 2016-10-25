<?php


/**
 * Decode POST data sent via Angular's $http service
 *
 * Due to the way Angular encodes POST data, PHP will not populate $_POST
 * automatically.
 *
 * @return array Post data
 */
function layotter_get_angular_post_data() {
    return (array) json_decode(file_get_contents('php://input'), true);
}






/**
 * Output the edit form for an element
 */
add_action('wp_ajax_layotter_edit_element', 'layotter_ajax_edit_element');
function layotter_ajax_edit_element() {
    if (isset($_POST['layotter_element_id']) AND ctype_digit($_POST['layotter_element_id']) AND $_POST['layotter_element_id'] != 0) {
        $layotter_element_id = (int)$_POST['layotter_element_id'];
        $element = Layotter::create_element_by_id($layotter_element_id);
        if ($element) {
            echo json_encode($element->get_form_data());
        }
    } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
        if (isset($_POST['layotter_values'])) {
            $values = $_POST['layotter_values'];
        } else {
            $values = array();
        }

        $element = Layotter::create_element($_POST['layotter_type'], $values);
        if ($element) {
            echo json_encode($element->get_form_data());
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Output JSON-encoded element data (called after editing an element)
 */
add_action('wp_ajax_layotter_parse_element', 'layotter_ajax_parse_element');
function layotter_ajax_parse_element() {
    if (isset($_POST['layotter_element_id']) AND ctype_digit($_POST['layotter_element_id']) AND $_POST['layotter_element_id'] != 0) {
        $old_id = (int)$_POST['layotter_element_id'];
        $id = wp_insert_post(array(
            'post_title' => 'Test revision',
            'post_type' => Layotter_Editable_Model::post_type,
            'meta_input' => array(
                'layotter_element_type' => get_post_meta($old_id, 'layotter_element_type', true)
            )
        ));

        $element = Layotter::create_element_by_id($id);
        if ($element) {
            echo json_encode($element->to_array());
        }
    } else if (isset($_POST['layotter_element_id']) AND isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
        $id = wp_insert_post(array(
            'post_title' => 'Test',
            'post_type' => Layotter_Editable_Model::post_type,
            'meta_input' => array(
                'layotter_element_type' => $_POST['layotter_type']
            )
        ));

        $element = Layotter::create_element_by_id($id);
        if ($element) {
            echo json_encode($element->to_array());
        }
    } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
        $values = Layotter_ACF::unwrap_post_values();
        $element = Layotter::create_element($_POST['layotter_type'], $values);
        if ($element) {
            echo json_encode($element->to_array());
        }
    }

    die(); // required by Wordpress after any AJAX call
}






/**
 * Output the edit form for post, row or element options
 */
add_action('wp_ajax_layotter_edit_options', 'layotter_ajax_edit_options');
function layotter_ajax_edit_options() {
    // type and option values are required
    if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
        if (isset($_POST['layotter_values'])) {
            $values = $_POST['layotter_values'];
        } else {
            $values = array();
        }

        if (isset($_POST['layotter_post_id'])) {
            $post_id = $_POST['layotter_post_id'];
        } else {
            $post_id = '';
        }

        $options = new Layotter_Options($_POST['layotter_type'], $values, $post_id);
        if ($options->is_enabled()) {
            echo json_encode($options->get_form_data());
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Output JSON-encoded options data (called after editing post, row or element options)
 */
add_action('wp_ajax_layotter_parse_options', 'layotter_ajax_parse_options');
function layotter_ajax_parse_options() {
    if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
        if (isset($_POST['layotter_post_id'])) {
            $post_id = $_POST['layotter_post_id'];
        } else {
            $post_id = '';
        }

        $values = Layotter_ACF::unwrap_post_values();
        $options = new Layotter_Options($_POST['layotter_type'], $values, $post_id);
        if($options->is_enabled()) {
            echo json_encode($options->to_array());
        }
    }

    die(); // required by Wordpress after any AJAX call
}






/**
 * Save element as a new template and output the new template's JSON-encoded data
 */
add_action('wp_ajax_layotter_save_new_template', 'layotter_ajax_save_new_template');
function layotter_ajax_save_new_template() {
    $post_data = layotter_get_angular_post_data();
    
    // type and field values are required
    if (isset($post_data['type']) AND is_string($post_data['type'])) {
        if (isset($post_data['values'])) {
            $values = $post_data['values'];
        } else {
            $values = array();
        }

        $element = Layotter::create_element($post_data['type'], $values);
        if ($element) {
            $template = Layotter_Templates::save($element);
            echo json_encode($template->to_array());
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Output the edit form for a template
 */
add_action('wp_ajax_layotter_edit_template', 'layotter_ajax_edit_template');
function layotter_ajax_edit_template() {
    $post_data = layotter_get_angular_post_data();

    // template ID is required
    if (isset($post_data['template_id']) AND is_int($post_data['template_id'])) {
        $element = Layotter_Templates::create_element($post_data['template_id']);
        if ($element) {
            echo json_encode($element->get_form_data());
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Update element template and output the template's JSON-encoded data
 */
add_action('wp_ajax_layotter_update_template', 'layotter_ajax_update_template');
function layotter_ajax_update_template() {
    $post_data = layotter_get_angular_post_data();
    
    // type and field values are required
    if (isset($post_data['template_id']) AND is_int($post_data['template_id'])) {
        $id = $post_data['template_id'];
        $template = Layotter_Templates::get($id);

        if ($template) {
            $values = Layotter_ACF::unwrap_post_values();

            $element = Layotter::create_element($template['type'], $values);
            if ($element) {
                $element->set_template_id($id);
                Layotter_Templates::update($id, $element->get_template_data());
                echo json_encode($element->to_array());
            }
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Delete a template
 */
add_action('wp_ajax_layotter_delete_template', 'layotter_ajax_delete_template');
function layotter_ajax_delete_template() {
    $post_data = layotter_get_angular_post_data();

    // template ID is required
    if (isset($post_data['template_id'])) {
        $template_object = Layotter_Templates::create_element($post_data['template_id']);
        if ($template_object) {
            Layotter_Templates::delete($post_data['template_id']);
            $template_object->unset_template_id();
            echo json_encode($template_object->to_array());
        }
    }

    die(); // required by Wordpress after any AJAX call
}






/**
 * Save JSON structure as a post layout
 */
add_action('wp_ajax_layotter_save_new_layout', 'layotter_ajax_save_new_layout');
function layotter_ajax_save_new_layout() {
    $post_data = layotter_get_angular_post_data();

    // name and JSON are required
    if (isset($post_data['name']) AND isset($post_data['json'])) {
        $layout = Layotter_Layouts::save($post_data['name'], $post_data['json']);
        echo json_encode($layout);
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Load a post layout
 */
add_action('wp_ajax_layotter_load_layout', 'layotter_ajax_load_layout');
function layotter_ajax_load_layout() {
    $post_data = layotter_get_angular_post_data();

    // template ID is required
    if (isset($post_data['layout_id'])) {
        $post = Layotter_Layouts::get($post_data['layout_id']);
        if ($post) {
            echo json_encode($post->to_array());
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Rename a post layout
 */
add_action('wp_ajax_layotter_rename_layout', 'layotter_ajax_rename_layout');
function layotter_ajax_rename_layout() {
    $post_data = layotter_get_angular_post_data();

    // template ID and new name are required
    if (isset($post_data['layout_id']) AND isset($post_data['name'])) {
        $renamed = Layotter_Layouts::rename($post_data['layout_id'], $post_data['name']);
        if ($renamed) {
            echo json_encode($renamed);
        }
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Delete a post layout
 */
add_action('wp_ajax_layotter_delete_layout', 'layotter_ajax_delete_layout');
function layotter_ajax_delete_layout() {
    $post_data = layotter_get_angular_post_data();

    // template ID is required
    if (isset($post_data['layout_id'])) {
        Layotter_Layouts::delete($post_data['layout_id']);
    }

    die(); // required by Wordpress after any AJAX call
}