<?php

namespace Layotter\Ajax;

use Layotter\Components\Layout;

/**
 * All Ajax requests arrive here
 */
class Layouts {

    /**
     * Save a new post layout
     *
     * @param array $data POST data
     * @return array Layout data
     */
    public static function create($data) {
        if (isset($data['name']) AND isset($data['json'])) {
            $json = stripslashes($data['json']);
            $layout = new Layout();
            $layout->set_json($json);
            $layout->save($data['name']);

            return $layout->to_array();
        }
    }

    /**
     * Output post layout data
     *
     * @param array $data POST data
     * @return array Layout data
     */
    public static function load($data) {
        if (isset($data['layout_id'])) {
            $layout = new Layout($data['layout_id']);

            return $layout->to_array();
        }
    }

    /**
     * Rename a post layout
     *
     * @param array $data POST data
     * @return array Layout data
     */
    public static function rename($data) {
        if (isset($data['layout_id']) AND isset($data['name'])) {
            $layout = new Layout($data['layout_id']);
            $layout->rename($data['name']);

            return $layout->to_array();
        }
    }

    /**
     * Delete a post layout
     *
     * @param array $data POST data
     */
    public static function delete($data) {
        if (isset($data['layout_id'])) {
            $layout = new Layout($data['layout_id']);
            $layout->delete();
        }
    }
}
