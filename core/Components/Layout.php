<?php

namespace Layotter\Components;

use Layotter\Core;
use Layotter\Errors;

/**
 * Layotter Posts turn into Layouts when they are saved to the database as templates for new posts.
 */
class Layout extends Post implements \JsonSerializable {

    /**
     * @var int Layout ID (e.g. post ID)
     */
    protected $layout_id = 0;

    /**
     * Create layout
     *
     * @param int $id Layout ID (0 for new layout)
     */
    public function __construct($id = 0) {
        if (!is_int($id)) {
            Errors::invalid_argument_not_recoverable('id');
        }

        parent::__construct($id);
        $this->layout_id = $id;
    }

    /**
     * Save layout to the database
     *
     * @param string $name Human readable name
     */
    public function save($name) {
        $name = (is_string($name) && !empty($name)) ? $name : __('Unnamed layout', 'layotter');

        $this->layout_id = wp_insert_post([
            'post_type' => Core::POST_TYPE_LAYOUT,
            'meta_input' => [
                Core::META_FIELD_JSON => addslashes(json_encode(parent::jsonSerialize())),
                Core::META_FIELD_MODEL_VERSION => Core::CURRENT_MODEL_VERSION
            ],
            'post_status' => 'publish',
            'post_title' => $name
        ]);
    }

    /**
     * Rename layout
     *
     * @param string $name New layout name
     */
    public function rename($name) {
        $name = (is_string($name) && !empty($name)) ? $name : __('Unnamed layout', 'layotter');

        wp_update_post([
            'ID' => $this->layout_id,
            'post_title' => $name
        ]);
    }

    /**
     * Delete layout from the database
     */
    public function delete() {
        wp_delete_post($this->layout_id);
    }

    /**
     * Return array representation for use in json_encode()
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'layout_id' => $this->layout_id,
            'name' => get_the_title($this->layout_id),
            'json' => parent::jsonSerialize(),
            'time_created' => get_the_date('U', $this->layout_id)
        ];
    }

    /**
     * Get this layout's ID
     *
     * @return int Post ID
     */
    public function get_id() {
        return $this->layout_id;
    }

}
