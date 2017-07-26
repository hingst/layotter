<?php

namespace Layotter\Ajax;

use Layotter\Core;

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
		        echo json_encode(Elements::edit($_POST));
		        break;
	        case 'save_element':
		        echo json_encode(Elements::save($_POST));
		        break;
	        case 'create_layout':
		        echo json_encode(Layouts::create($_POST));
		        break;
	        case 'load_layout':
		        echo json_encode(Layouts::load($_POST));
		        break;
	        case 'rename_layout':
		        echo json_encode(Layouts::rename($_POST));
		        break;
	        case 'delete_layout':
		        Layouts::delete($_POST);
		        break;
	        case 'edit_options':
		        echo json_encode(Options::edit($_POST));
		        break;
	        case 'save_options':
		        echo Options::save($_POST);
		        break;
	        case 'create_template':
		        echo json_encode(Templates::create($_POST));
		        break;
	        case 'delete_template':
		        echo json_encode(Templates::delete($_POST));
		        break;
        }

        die();
    }
}
