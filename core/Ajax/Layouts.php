<?php

namespace Layotter\Ajax;

use Layotter\Components\Layout;
use Layotter\Errors;

/**
 * Handles Ajax requests concerning layouts
 */
class Layouts {

    /**
     * Save a new post layout
     *
     * @param array $data POST data
     * @return Layout Layout data
     */
    public static function create($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_name']) && is_string($data['layotter_name']) && isset($data['layotter_json']) && is_string($data['layotter_json'])) {
            $json = stripslashes($data['layotter_json']);
            $layout = new Layout();
            $layout->set_json($json);
            $layout->save($data['layotter_name']);
            return $layout;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_name or layotter_json');
            return null;
        }
    }

    /**
     * Output post layout data
     *
     * @param array $data POST data
     * @return Layout Layout data
     */
    public static function load($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && Handler::is_positive_int($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $layout = new Layout($id);
            return $layout;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id');
            return null;
        }
    }

    /**
     * Rename a post layout
     *
     * @param array $data POST data
     * @return Layout Layout data
     */
    public static function rename($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && Handler::is_positive_int($data['layotter_id']) && isset($data['layotter_name']) && is_string($data['layotter_name'])) {
            $id = intval($data['layotter_id']);
            $layout = new Layout($id);
            $layout->rename($data['layotter_name']);
            return $layout;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id or layotter_name');
            return null;
        }
    }

    /**
     * Delete a post layout
     *
     * @param array $data POST data
     */
    public static function delete($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && Handler::is_positive_int($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $layout = new Layout($id);
            $layout->delete();
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id');
        }
    }
}
