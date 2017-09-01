<?php

namespace Layotter\Ajax;

use Layotter\Core;
use Layotter\Errors;
use Layotter\Structures\FormMeta;

/**
 * Handles Ajax requests concerning options
 */
class Options {

    /**
     * Output the edit form for post, row, col or element options
     *
     * @param array $data POST data
     * @return FormMeta Form meta data
     */
    public static function edit($data = null) {
        $data = is_array($data) ? $data : $_POST;
        $post_type_context = null;

        if (isset($data['layotter_post_id']) && Handler::is_positive_int($data['layotter_post_id'])) {
            $post_type_context = get_post_type($data['layotter_post_id']);
        }

        if (isset($data['layotter_options_id']) && Handler::is_positive_int($data['layotter_options_id'])) {
            $id = intval($data['layotter_options_id']);
            $options = Core::assemble_options($id);
            $options->set_post_type_context($post_type_context);
            return $options->get_form_meta();
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            $type = $data['layotter_type'];
            $options = Core::assemble_new_options($type);
            $options->set_post_type_context($post_type_context);
            return $options->get_form_meta();
        } else {
            Errors::invalid_argument_not_recoverable('layotter_options_id or layotter_type');
        }
    }

    /**
     * Save options
     *
     * @param array $data POST data
     * @return string Options ID
     */
    public static function save($data = null) {
        $data = is_array($data) ? $data : $_POST;

        if (isset($data['layotter_options_id']) && Handler::is_positive_int($data['layotter_options_id'])) {
            $id = intval($data['layotter_options_id']);
            $options = Core::assemble_options($id);
            $options->save_from_post_data();
            return $options->get_id();
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            $options = Core::assemble_new_options($data['layotter_type']);
            $options->save_from_post_data();
            return $options->get_id();
        } else {
            Errors::invalid_argument_not_recoverable('layotter_options_id or layotter_type');
        }
    }
}
