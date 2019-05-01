<?php

namespace Layotter\Ajax;

use Layotter\Core;
use Layotter\Errors;

/**
 * Handles ajax calls concerning templates.
 */
class TemplatesHandler {

    /**
     * Saves an existing element as a new template and prints the new template as JSON.
     *
     * @param array $data POST data.
     */
    public static function create($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && RequestManager::is_valid_id($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $element = Core::assemble_element($id);
            $element->set_template(true);
            $result = $element;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id');
            $result = null;
        }

        echo json_encode($result);
    }

    /**
     * Deletes a template and prints the resulting plain element (without template flag) as JSON.
     *
     * @param array $data POST data.
     */
    public static function delete($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && RequestManager::is_valid_id($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $element = Core::assemble_element($id);
            $element->set_template(false);
            $result = $element;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id');
            $result = null;
        }

        echo json_encode($result);
    }
}
