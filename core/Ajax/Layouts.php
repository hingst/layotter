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
     * @return Layout Layout data
     */
    public static function create($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['name']) AND isset($data['json'])) {
            $json = stripslashes($data['json']);
            $layout = new Layout();
            $layout->set_json($json);
            $layout->save($data['name']);
            return $layout;
        }
    }

    /**
     * Output post layout data
     *
     * @param array $data POST data
     * @return Layout Layout data
     */
    public static function load($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['layout_id'])) {
            $layout = new Layout($data['layout_id']);
            return $layout;
        }
    }

    /**
     * Rename a post layout
     *
     * @param array $data POST data
     * @return Layout Layout data
     */
    public static function rename($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['layout_id']) AND isset($data['name'])) {
            $layout = new Layout($data['layout_id']);
            $layout->rename($data['name']);
            return $layout;
        }
    }

    /**
     * Delete a post layout
     *
     * @param array $data POST data
     */
    public static function delete($data = null) {
        $data = is_null($data) ? $_POST : $data;
        if (isset($data['layout_id'])) {
            $layout = new Layout($data['layout_id']);
            $layout->delete();
        }
    }
}
