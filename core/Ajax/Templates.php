<?php

namespace Layotter\Ajax;

use Layotter\Components\Element;
use Layotter\Core;

/**
 * All Ajax requests arrive here
 */
class Templates {

    /**
     * Save element as a new template
     *
     * @param array $data POST data
     * @return Element Template data
     */
    public static function create($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['id']) AND ctype_digit($data['id'])) {
            $element = Core::assemble_element($data['id']);
            $element->set_template(true);
            return $element;
        }
    }

    /**
     * Delete a template
     *
     * @param array $data POST data
     * @return Element Element data
     */
    public static function delete($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['layotter_element_id']) AND ctype_digit($data['layotter_element_id']) AND $data['layotter_element_id'] != 0) {
            $id = intval($data['layotter_element_id']);
            $element = Core::assemble_element($id);
            $element->set_template(false);
            return $element;
        }
    }
}
