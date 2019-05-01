<?php

namespace Layotter\Ajax;

use Layotter\Core;
use Layotter\Errors;

/**
 * Handles ajax calls concerning elements.
 */
class ElementsHandler {

    /**
     * Prints meta data for an element's edit form as JSON.
     *
     * @param array $data POST data.
     */
    public static function edit($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && RequestManager::is_valid_id($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $element = Core::assemble_element($id);
            $result = $element->get_form_meta();
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            $type = $data['layotter_type'];
            $element = Core::assemble_new_element($type);
            $result = $element->get_form_meta();
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id or layotter_type');
            $result = null;
        }

        echo json_encode($result);
    }

    /**
     * Saves field values from POST data to an element and prints the updated element as JSON.
     *
     * @param array $data POST data.
     */
    public static function save($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && RequestManager::is_valid_id($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $element = Core::assemble_element($id);
            if ($element->is_template()) {
                $element->update_from_post_data();
            } else {
                $element->save_from_post_data();
            }
            $result = $element;
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            $element = Core::assemble_new_element($data['layotter_type']);
            $element->save_from_post_data();
            $result = $element;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id or layotter_type');
            $result = null;
        }

        echo json_encode($result);
    }
}
