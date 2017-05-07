<?php

namespace Layotter\Ajax;

use Layotter\Core;

/**
 * All Ajax requests arrive here
 */
class Options {

    /**
     * Output the edit form for post, row, col or element options
     */
    public static function edit() {
        if (isset($_POST['layotter_post_id'])) {
            $post_type_context = get_post_type($_POST['layotter_post_id']);
        } else {
            $post_type_context = '';
        }

        if (isset($_POST['layotter_options_id']) AND ctype_digit($_POST['layotter_options_id']) AND $_POST['layotter_options_id'] != 0) {
            $id = intval($_POST['layotter_options_id']);
            $options = Core::assemble_options($id);
            $options->set_post_type_context($post_type_context);
            echo $options->get_form_json();
        } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
            $type = $_POST['layotter_type'];
            $options = Core::assemble_new_options($type);
            $options->set_post_type_context($post_type_context);
            echo $options->get_form_json();
        }

        die();
    }

    /**
     * Save options
     */
    public static function save() {
        if (isset($_POST['layotter_post_id'])) {
            $post_type_context = get_post_type($_POST['layotter_post_id']);
        } else {
            $post_type_context = '';
        }

        if (isset($_POST['layotter_options_id']) AND ctype_digit($_POST['layotter_options_id']) AND $_POST['layotter_options_id'] != 0) {
            $id = intval($_POST['layotter_options_id']);
            $options = Core::assemble_options($id);
            $options->set_post_type_context($post_type_context);
            $options->save_from_post_data();
            echo $options->get_id();
        } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
            $options = Core::assemble_new_options($_POST['layotter_type']);
            $options->set_post_type_context($post_type_context);
            $options->save_from_post_data();
            echo $options->get_id();
        }

        die();
    }
}
