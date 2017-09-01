<?php

namespace Layotter\Ajax;

use Layotter\Components\Element;
use Layotter\Core;
use Layotter\Errors;

/**
 * Handles Ajax requests concerning elements
 */
class Templates {

    /**
     * Save element as a new template
     *
     * @param array $data POST data
     * @return Element Template data
     */
    public static function create($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && Handler::is_positive_int($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $element = Core::assemble_element($id);
            $element->set_template(true);
            return $element;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id');
        }
    }

    /**
     * Delete a template
     *
     * @param array $data POST data
     * @return Element Element data
     */
    public static function delete($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && Handler::is_positive_int($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $element = Core::assemble_element($id);
            $element->set_template(false);
            return $element;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id');
        }
    }
}
