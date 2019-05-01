<?php

namespace Layotter\Ajax;

use Layotter\Core;
use Layotter\Errors;

/**
 * Handles ajax calls concerning options.
 */
class OptionsHandler {

    /**
     * Prints the edit form for post, row, column or element options as JSON.
     *
     * @param array $data POST data.
     */
    public static function edit($data = null) {
        $data = is_array($data) ? $data : $_POST;
        $post_type_context = null;

        if (isset($data['layotter_post_id']) && RequestManager::is_valid_id($data['layotter_post_id'])) {
            $post_type_context = get_post_type($data['layotter_post_id']);
        }

        if (isset($data['layotter_options_id']) && RequestManager::is_valid_id($data['layotter_options_id'])) {
            $id = intval($data['layotter_options_id']);
            $options = Core::assemble_options($id);
            $options->set_post_type_context($post_type_context);
            $result = $options->get_form_meta();
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            $type = $data['layotter_type'];
            $options = Core::assemble_new_options($type);
            $options->set_post_type_context($post_type_context);
            $result = $options->get_form_meta();
        } else {
            Errors::invalid_argument_not_recoverable('layotter_options_id or layotter_type');
            $result = null;
        }

        echo json_encode($result);
    }

    /**
     * Saves field values from POST data to options and prints the options ID.
     *
     * @param array $data POST data.
     */
    public static function save($data = null) {
        $data = is_array($data) ? $data : $_POST;

        if (isset($data['layotter_options_id']) && RequestManager::is_valid_id($data['layotter_options_id'])) {
            $id = intval($data['layotter_options_id']);
            $options = Core::assemble_options($id);
            $options->save_from_post_data();
            $result = $options->get_id();
        } else if (isset($data['layotter_type']) && is_string($data['layotter_type'])) {
            $options = Core::assemble_new_options($data['layotter_type']);
            $options->save_from_post_data();
            $result = $options->get_id();
        } else {
            Errors::invalid_argument_not_recoverable('layotter_options_id or layotter_type');
            $result = null;
        }

        // TODO: why print just the ID and not the whole JSON?

        echo json_encode($result);
    }
}
