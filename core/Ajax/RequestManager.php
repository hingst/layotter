<?php

namespace Layotter\Ajax;

use Exception;

class RequestManager {

    public static function register() {
        add_action('wp_ajax_layotter', [__CLASS__, 'handle']);
    }

    /**
     * @throws Exception
     */
    public static function handle() {
        switch ($_POST['layotter_action']) {
            case 'edit_element':
                $result = ElementsHandler::edit();
                break;
            case 'save_element':
                $result = ElementsHandler::save();
                break;
            case 'create_layout':
                $result = LayoutsHandler::create();
                break;
            case 'load_layout':
                $result = LayoutsHandler::load();
                break;
            case 'rename_layout':
                $result = LayoutsHandler::rename();
                break;
            case 'delete_layout':
                $result = LayoutsHandler::delete();
                break;
            case 'edit_options':
                $result = OptionsHandler::edit();
                break;
            case 'save_options':
                $result = OptionsHandler::save();
                break;
            case 'create_template':
                $result = TemplatesHandler::create();
                break;
            case 'delete_template':
                $result = TemplatesHandler::delete();
                break;
            default:
                $result = false;
        }

        echo json_encode($result);
        die();
    }

    /**
     * @param $check string
     * @return bool
     */
    public static function is_valid_id($check) {
        return (ctype_digit($check) && intval($check) > 0);
    }
}
