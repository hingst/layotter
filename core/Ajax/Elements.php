<?php

namespace Layotter\Ajax;

use Layotter\Core;

/**
 * All Ajax requests arrive here
 */
class Elements {

    /**
     * Output the edit form for an element
     *
     * @param array $data POST data
     * @return array Form data
     */
    public static function edit($data) {
        if (isset($data['layotter_element_id']) AND ctype_digit($data['layotter_element_id']) AND $data['layotter_element_id'] != 0) {
            $id = intval($data['layotter_element_id']);
            $element = Core::assemble_element($id);
            return $element->get_form_data();
        } else if (isset($data['layotter_type']) AND is_string($data['layotter_type'])) {
            $type = $data['layotter_type'];
            $element = Core::assemble_new_element($type);
            return $element->get_form_data();
        }
    }

    /**
     * Save an element
     *
     * @param array $data POST data
     * @return array Element data
     */
    public static function save($data) {
        if (isset($data['layotter_element_id']) AND ctype_digit($data['layotter_element_id']) AND $data['layotter_element_id'] != 0) {
            $id = intval($data['layotter_element_id']);
            $element = Core::assemble_element($id);
            if ($element->is_template()) {
                $element->update_from_post_data();
            } else {
                $element->save_from_post_data();
            }
            return $element->to_array();
        } else if (isset($data['layotter_type']) AND is_string($data['layotter_type'])) {
            $element = Core::assemble_new_element($data['layotter_type']);
            $element->save_from_post_data();
            return $element->to_array();
        }
    }
}
