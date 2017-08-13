<?php

namespace Layotter\Ajax;

use Layotter\Components\Element;
use Layotter\Core;
use Layotter\Errors;
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
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && Handler::is_positive_int($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $element = Core::assemble_element($id);
            return $element->get_form_meta();
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            $type = $data['layotter_type'];
            $element = Core::assemble_new_element($type);
            return $element->get_form_meta();
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id or layotter_type');
        }
    }

    /**
     * Save an element
     *
     * @param array $data POST data
     * @return Element Element data
     */
    public static function save($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && Handler::is_positive_int($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
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
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id or layotter_type');
        }
    }
}
