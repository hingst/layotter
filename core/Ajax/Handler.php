<?php

namespace Layotter\Ajax;

/**
 * All Ajax requests arrive here
 */
class Handler {

    /**
     * Output the edit form for an element
     */
    public static function handle() {
        switch ($_POST['layotter_action']) {
            case 'edit_element':
                echo json_encode(Elements::edit());
                break;
            case 'save_element':
                echo json_encode(Elements::save());
                break;
            case 'create_layout':
                echo json_encode(Layouts::create());
                break;
            case 'load_layout':
                echo json_encode(Layouts::load());
                break;
            case 'rename_layout':
                echo json_encode(Layouts::rename());
                break;
            case 'delete_layout':
                Layouts::delete();
                break;
            case 'edit_options':
                echo json_encode(Options::edit());
                break;
            case 'save_options':
                echo Options::save();
                break;
            case 'create_template':
                echo json_encode(Templates::create());
                break;
            case 'delete_template':
                echo json_encode(Templates::delete());
                break;
        }

        die();
    }

    /**
     * Check if a POST string variable contains only a positive integer
     *
     * @param $check string Value to check
     * @return bool
     */
    public static function is_positive_int($check) {
        return (ctype_digit($check) && intval($check) !== 0);
    }
}
