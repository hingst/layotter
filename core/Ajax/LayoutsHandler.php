<?php

namespace Layotter\Ajax;

use Layotter\Components\Layout;
use Layotter\Errors;

/**
 * Handles ajax calls concerning layouts.
 */
class LayoutsHandler {

    /**
     * Saves a new post layout and prints the saved layout as JSON.
     *
     * @param array $data POST data.
     */
    public static function create($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_name'], $data['layotter_json']) && is_string($data['layotter_name']) && is_string($data['layotter_json'])) {
            $json = stripslashes($data['layotter_json']);
            $layout = new Layout();
            $layout->set_json($json);
            $layout->save($data['layotter_name']);
            $result = $layout;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_name or layotter_json');
            $result = null;
        }

        echo json_encode($result);
    }

    /**
     * Prints an existing post layout as JSON.
     *
     * @param array $data POST data.
     */
    public static function load($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && RequestManager::is_valid_id($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $layout = new Layout($id);
            $result = $layout;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id');
            $result = null;
        }

        echo json_encode($result);
    }

    /**
     * Renames an existing post layout and prints the updated layout as JSON.
     *
     * @param array $data POST data.
     */
    public static function rename($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id'], $data['layotter_name']) && RequestManager::is_valid_id($data['layotter_id']) && is_string($data['layotter_name'])) {
            $id = intval($data['layotter_id']);
            $layout = new Layout($id);
            $layout->rename($data['layotter_name']);
            $result = $layout;
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id or layotter_name');
            $result = null;
        }

        echo json_encode($result);
    }

    /**
     * Deletes an existing post layout.
     *
     * @param array $data POST data.
     */
    public static function delete($data = null) {
        $data = is_array($data) ? $data : $_POST;
        if (isset($data['layotter_id']) && RequestManager::is_valid_id($data['layotter_id'])) {
            $id = intval($data['layotter_id']);
            $layout = new Layout($id);
            $layout->delete();
        } else {
            Errors::invalid_argument_not_recoverable('layotter_id');
        }

        // TODO: print result for error handling in JS?
    }
}
