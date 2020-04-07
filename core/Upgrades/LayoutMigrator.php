<?php

namespace Layotter\Upgrades;

use InvalidArgumentException;
use Layotter\Initializer;

class LayoutMigrator {

    private $id;

    public function __construct($id) {
        if (!is_int($id)) {
            throw new InvalidArgumentException();
        }

        $this->id = $id;
    }

    public function migrate() {
        $layouts = get_option('layotter_post_layouts');

        // check if a layout exists for the ID
        // deleted layouts remain in the database as null values, so check with is_array()
        if (is_array($layouts) && isset($layouts[$this->id]) && is_array($layouts[$this->id])) {
            $layout = $layouts[$this->id];

            $id = wp_insert_post([
                'post_type' => Initializer::POST_TYPE_LAYOUT,
                'meta_input' => [
                    Initializer::META_FIELD_JSON => addslashes($layout['json']),
                    Initializer::META_FIELD_MODEL_VERSION => Initializer::MODEL_VERSION
                ],
                'post_status' => 'publish',
                'post_title' => $layout['name'],
                'post_date' => date('Y-m-d H:i:s', $layout['time_created']),
                'post_date_gmt' => get_gmt_from_date($layout['time_created'])
            ]);

            $post = new PostMigrator($id);
            $post->migrate();

            $layouts[$this->id] = null;
            update_option('layotter_post_layouts', $layouts);
        }
    }

}