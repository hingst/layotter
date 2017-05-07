<?php

namespace Layotter\Ajax;

use Layotter\Components\Layout;

/**
 * All Ajax requests arrive here
 */
class Layouts {

    /**
     * Save a new post layout
     */
    public static function create() {
        if (isset($_POST['name']) AND isset($_POST['json'])) {
            $json = stripslashes($_POST['json']);
            $layout = new Layout();
            $layout->set_json($json);
            $layout->save($_POST['name']);
            echo $layout->to_json();
        }

        die();
    }

    /**
     * Output post layout data
     */
    public static function load() {
        if (isset($_POST['layout_id'])) {
            $layout = new Layout($_POST['layout_id']);
            echo $layout->to_json();
        }

        die();
    }

    /**
     * Rename a post layout
     */
    public static function rename() {
        if (isset($_POST['layout_id']) AND isset($_POST['name'])) {
            $layout = new Layout($_POST['layout_id']);
            $layout->rename($_POST['name']);
            echo $layout->to_json();
        }

        die();
    }

    /**
     * Delete a post layout
     */
    public static function delete() {
        if (isset($_POST['layout_id'])) {
            $layout = new Layout($_POST['layout_id']);
            $layout->delete();
        }

        die();
    }
}
