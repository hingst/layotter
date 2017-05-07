<?php

namespace Layotter\Ajax;

use Layotter\Core;

/**
 * All Ajax requests arrive here
 */
class Elements {

    /**
     * Output the edit form for an element
     */
    public static function edit() {
        if (isset($_POST['layotter_element_id']) AND ctype_digit($_POST['layotter_element_id']) AND $_POST['layotter_element_id'] != 0) {
            $id = intval($_POST['layotter_element_id']);
            $element = Core::assemble_element($id);
            echo $element->get_form_json();
        } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
            $type = $_POST['layotter_type'];
            $element = Core::assemble_new_element($type);
            echo $element->get_form_json();
        }

        die();
    }

    /**
     * Save an element
     */
    public static function save() {
        if (isset($_POST['layotter_element_id']) AND ctype_digit($_POST['layotter_element_id']) AND $_POST['layotter_element_id'] != 0) {
            $id = intval($_POST['layotter_element_id']);
            $element = Core::assemble_element($id);
            if ($element->is_template()) {
                $element->update_from_post_data();
            } else {
                $element->save_from_post_data();
            }
            echo $element->to_json();
        } else if (isset($_POST['layotter_type']) AND is_string($_POST['layotter_type'])) {
            $element = Core::assemble_new_element($_POST['layotter_type']);
            $element->save_from_post_data();
            echo $element->to_json();
        }

        die();
    }
}
