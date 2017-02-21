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
        $id = intval($_POST['layotter_element_id']);
        $element = Layotter::assemble_element($id);
        echo $element->get_form_json();
    } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
        $type = $_POST['layotter_type'];
        $element = Layotter::assemble_new_element($type);
        echo $element->get_form_json();
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Output JSON-encoded element data (c<alled after editing an element)
 */
add_action('wp_ajax_layotter_save_element', 'layotter_ajax_save_element');
function layotter_ajax_save_element() {
    if (isset($_POST['layotter_element_id']) AND ctype_digit($_POST['layotter_element_id']) AND $_POST['layotter_element_id'] != 0) {
        $id = intval($_POST['layotter_element_id']);
        $element = Layotter::assemble_element($id);
        if ($element->is_template()) {
            $element->update_from_post_data();
        } else {
            $element->save_from_post_data();
        }
        echo $element->to_json();
    } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
        $element = Layotter::assemble_new_element($_POST['layotter_type']);
        $element->save_from_post_data();
        echo $element->to_json();
    }

    die(); // required by Wordpress after any AJAX call
}






/**
 * Output the edit form for post, row or element options
 */
add_action('wp_ajax_layotter_edit_options', 'layotter_ajax_edit_options');
function layotter_ajax_edit_options() {
    if (isset($_POST['layotter_post_id'])) {
        $post_type_context = get_post_type($_POST['layotter_post_id']);
    } else {
        $post_type_context = '';
    }

    if (isset($_POST['layotter_options_id']) AND ctype_digit($_POST['layotter_options_id']) AND $_POST['layotter_options_id'] != 0) {
        $id = intval($_POST['layotter_options_id']);
        $options = Layotter::assemble_options($id);
        $options->set_post_type_context($post_type_context);
        echo $options->get_form_json();
    } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
        $type = $_POST['layotter_type'];
        $options = Layotter::assemble_new_options($type);
        $options->set_post_type_context($post_type_context);
        echo $options->get_form_json();
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Output JSON-encoded options data (called after editing post, row or element options)
 */
add_action('wp_ajax_layotter_save_options', 'layotter_ajax_save_options');
function layotter_ajax_save_options() {
    if (isset($_POST['layotter_post_id'])) {
        $post_type_context = get_post_type($_POST['layotter_post_id']);
    } else {
        $post_type_context = '';
    }

    if (isset($_POST['layotter_options_id']) AND ctype_digit($_POST['layotter_options_id']) AND $_POST['layotter_options_id'] != 0) {
        $id = intval($_POST['layotter_options_id']);
        $options = Layotter::assemble_options($id);
        $options->set_post_type_context($post_type_context);
        $options->save_from_post_data();
        echo $options->to_json();
    } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
        $options = Layotter::assemble_new_options($_POST['layotter_type']);
        $options->set_post_type_context($post_type_context);
        $options->save_from_post_data();
        echo $options->to_json();
    }

    die(); // required by Wordpress after any AJAX call
}






/**
 * Save element as a new template and output the new template's JSON-encoded data
 */
add_action('wp_ajax_layotter_save_new_template', 'layotter_ajax_save_new_template');
function layotter_ajax_save_new_template() {
    if (isset($_POST['id']) AND ctype_digit($_POST['id'])) {
        $element = Layotter::assemble_element($_POST['id']);
        $element->set_template(true);
        echo $element->to_json();
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Delete a template
 */
add_action('wp_ajax_layotter_delete_template', 'layotter_ajax_delete_template');
function layotter_ajax_delete_template() {
    if (isset($_POST['layotter_element_id']) AND ctype_digit($_POST['layotter_element_id']) AND $_POST['layotter_element_id'] != 0) {
        $id = intval($_POST['layotter_element_id']);
        $element = Layotter::assemble_element($id);
        $element->set_template(false);
        echo $element->to_json();
    }

    die(); // required by Wordpress after any AJAX call
}






/**
 * Save JSON structure as a post layout
 */
add_action('wp_ajax_layotter_save_new_layout', 'layotter_ajax_save_new_layout');
function layotter_ajax_save_new_layout() {
    $_POST = stripslashes_deep($_POST);
    if (isset($_POST['name']) AND isset($_POST['json'])) {
        $layout = Layotter_Layouts::save($_POST['name'], $_POST['json']);
        echo json_encode($layout);
    }

    die(); // required by Wordpress after any AJAX call
}


/**
 * Load a post layout
 */
add_action('wp_ajax_layotter_load_layout', 'layotter_ajax_load_layout');
function layotter_ajax_load_layout() {
    if (isset($_POST['layout_id'])) {
        $post = Layotter_Layouts::get($_POST['layout_id']);
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
    if (isset($_POST['layout_id']) AND isset($_POST['name'])) {
        $renamed = Layotter_Layouts::rename($_POST['layout_id'], $_POST['name']);
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
    if (isset($_POST['layout_id'])) {
        Layotter_Layouts::delete($_POST['layout_id']);
    }

    die(); // required by Wordpress after any AJAX call
}