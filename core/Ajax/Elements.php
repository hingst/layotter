<?php

namespace Layotter\Ajax;

use Layotter\Components\Element;
use Layotter\Core;
use Layotter\Structures\FormMeta;

/**
 * All Ajax requests arrive here
 */
class Elements {

    /**
     * Output the edit form for an element
     *
     * @param array $data POST data
     * @return FormMeta Form meta data
     */
    public static function edit($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['layotter_element_id']) && ctype_digit($data['layotter_element_id']) && $data['layotter_element_id'] != 0) {
            $id = intval($data['layotter_element_id']);
            $element = Core::assemble_element($id);
            return $element->get_form_meta();
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            $type = $data['layotter_type'];
            $element = Core::assemble_new_element($type);
            return $element->get_form_meta();
        }
    }

    /**
     * Save an element
     *
     * @param array $data POST data
     * @return Element Element data
     */
    public static function save($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['layotter_element_id']) && ctype_digit($data['layotter_element_id']) && $data['layotter_element_id'] != 0) {
            $id = intval($data['layotter_element_id']);
            $element = Core::assemble_element($id);
            if ($element->is_template()) {
                $element->update_from_post_data();
            } else {
                $element->save_from_post_data();
            }
            return $element;
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            $element = Core::assemble_new_element($data['layotter_type']);
            $element->save_from_post_data();
            return $element;
        }
    }
}
