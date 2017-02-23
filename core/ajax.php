<?php

class Layotter_Ajax_Endpoints {
    /**
     * Output the edit form for an element
     */
    public static function edit_element() {
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
    public static function save_element() {
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
    public static function edit_options() {
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
    public static function save_options() {
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
    public static function save_new_template() {
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
    public static function delete_template() {
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
    public static function save_new_layout() {
        if (isset($_POST['name']) AND isset($_POST['json'])) {
            $json = stripslashes($_POST['json']);
            $layout = new Layotter_Layout();
            $layout->set_json($json);
            $layout->save($_POST['name']);
            echo $layout->to_json();
        }

        die(); // required by Wordpress after any AJAX call
    }
    
    /**
     * Load a post layout
     */
    public static function load_layout() {
        if (isset($_POST['layout_id'])) {
            $layout = new Layotter_Layout($_POST['layout_id']);
            echo $layout->to_json();
        }

        die(); // required by Wordpress after any AJAX call
    }
    
    /**
     * Rename a post layout
     */
    public static function rename_layout() {
        if (isset($_POST['layout_id']) AND isset($_POST['name'])) {
            $layout = new Layotter_Layout($_POST['layout_id']);
            $layout->rename($_POST['name']);
            echo $layout->to_json();
        }

        die(); // required by Wordpress after any AJAX call
    }
    
    /**
     * Delete a post layout
     */
    public static function delete_layout() {
        if (isset($_POST['layout_id'])) {
            $layout = new Layotter_Layout($_POST['layout_id']);
            $layout->delete();
        }

        die(); // required by Wordpress after any AJAX call
    }
}