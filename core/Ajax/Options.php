<?php

namespace Layotter\Ajax;

use Layotter\Core;
use Layotter\Structures\FormMeta;

/**
 * All Ajax requests arrive here
 */
class Options {

    /**
     * Output the edit form for post, row, col or element options
     *
     * @param array $data POST data
     * @return FormMeta Form meta data
     */
    public static function edit($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['layotter_post_id'])) {
            $post_type_context = get_post_type($data['layotter_post_id']);
        } else {
            $post_type_context = '';
        }

        if (isset($data['layotter_options_id']) AND ctype_digit($data['layotter_options_id']) AND $data['layotter_options_id'] != 0) {
            $id = intval($data['layotter_options_id']);
            $options = Core::assemble_options($id);
            $options->set_post_type_context($post_type_context);
            return $options->get_form_meta();
        } else if (isset($data['layotter_type']) AND is_string($data['layotter_type'])) {
            $type = $data['layotter_type'];
            $options = Core::assemble_new_options($type);
            $options->set_post_type_context($post_type_context);
            return $options->get_form_meta();
        }
    }

    /**
     * Save options
     *
     * @param array $data POST data
     * @return string Options ID
     */
    public static function save($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['layotter_post_id'])) {
            $post_type_context = get_post_type($data['layotter_post_id']);
        } else {
            $post_type_context = '';
        }

        if (isset($data['layotter_options_id']) AND ctype_digit($data['layotter_options_id']) AND $data['layotter_options_id'] != 0) {
            $id = intval($data['layotter_options_id']);
            $options = Core::assemble_options($id);
            $options->set_post_type_context($post_type_context);
            $options->save_from_post_data();
            return $options->get_id();
        } else if (isset($data['layotter_type']) AND is_string($data['layotter_type'])) {
            $options = Core::assemble_new_options($data['layotter_type']);
            $options->set_post_type_context($post_type_context);
            $options->save_from_post_data();
            return $options->get_id();
        }
    }
}
