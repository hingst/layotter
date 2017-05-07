<?php

namespace Layotter\Ajax;

use Layotter\Core;

/**
 * All Ajax requests arrive here
 */
class Templates {

    /**
     * Save element as a new template
     */
    public static function create() {
        if (isset($_POST['id']) AND ctype_digit($_POST['id'])) {
            $element = Core::assemble_element($_POST['id']);
            $element->set_template(true);
            echo $element->to_json();
        }

        die();
    }

    /**
     * Delete a template
     */
    public static function delete() {
        if (isset($_POST['layotter_element_id']) AND ctype_digit($_POST['layotter_element_id']) AND $_POST['layotter_element_id'] != 0) {
            $id = intval($_POST['layotter_element_id']);
            $element = Core::assemble_element($id);
            $element->set_template(false);
            echo $element->to_json();
        }

        die();
    }
}
