<?php

namespace Layotter\Upgrades;

use Layotter\Components\Layout;

class LayoutMigrator {

    private $id;

    public function __construct($id) {
        $this->id = intval($id);
    }

    public function migrate() {
        $layouts = get_option('layotter_post_layouts');

        // check if a layout exists for the ID
        // deleted layouts remain in the database as null values, so check with is_array()
        if (is_array($layouts) AND isset($layouts[$this->id]) AND is_array($layouts[$this->id])) {
            $layout = $layouts[$this->id];

            $id = wp_insert_post(array(
                'post_type' => Layout::POST_TYPE_LAYOUTS,
                'meta_input' => array(
                    'layotter_json' => addslashes($layout['json'])
                ),
                'post_status' => 'publish',
                'post_title' => $layout['name'],
                'post_date' => date('Y-m-d H:i:s', $layout['time_created']),
                'post_date_gmt' => get_gmt_from_date($layout['time_created'])
            ));

            $post = new PostMigrator($id);
            $post->migrate();

            $layouts[$this->id] = null;
            update_option('layotter_post_layouts', $layouts);
        }
    }

}