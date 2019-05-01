<?php

namespace Layotter\Ajax;

/**
 * Handles all of Layotter's ajax requests in the admin panel and routes them to the proper handlers.
 */
class RequestManager {

    /**
     * Registers Layotter's central ajax handler.
     */
    public static function register() {
        add_action('wp_ajax_layotter', [__CLASS__, 'handle']);
    }

    /**
     * Routes an incoming request to the proper method.
     */
    public static function handle() {
        switch ($_POST['layotter_action']) {
            case 'edit_element':
                ElementsHandler::edit();
                break;
            case 'save_element':
                ElementsHandler::save();
                break;
            case 'create_layout':
                LayoutsHandler::create();
                break;
            case 'load_layout':
                LayoutsHandler::load();
                break;
            case 'rename_layout':
                LayoutsHandler::rename();
                break;
            case 'delete_layout':
                LayoutsHandler::delete();
                break;
            case 'edit_options':
                OptionsHandler::edit();
                break;
            case 'save_options':
                OptionsHandler::save();
                break;
            case 'create_template':
                TemplatesHandler::create();
                break;
            case 'delete_template':
                TemplatesHandler::delete();
                break;
        }

        die();
    }

    /**
     * Checks if a given string contains a valid ID (i.e. a positive integer).
     *
     * TODO: move this utility function to a better place
     *
     * @param $check string The value to check.
     * @return bool
     */
    public static function is_valid_id($check) {
        return (ctype_digit($check) && intval($check) > 0);
    }
}
