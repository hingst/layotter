<?php

/**
 * Layotter_Posts turn into Layotter_Layouts when they are
 * saved to the database as templates for new posts.
 */
class Layotter_Layout extends Layotter_Post {

    const POST_TYPE_LAYOUTS = 'layotter_post_layout';

    protected $layout_id = 0;

    /**
     * Create layout
     *
     * @param int $id Layout ID (0 for new layout)
     */
    public function __construct($id = 0) {
        parent::__construct($id);
        $this->layout_id = $id;
    }

    /**
     * Save layout to the database
     *
     * @param string $name Human readable name
     */
    public function save($name) {
        $this->layout_id = wp_insert_post(array(
            'post_type' => self::POST_TYPE_LAYOUTS,
            'meta_input' => array(
                Layotter::META_FIELD_JSON => $this->json,
            ),
            'post_status' => 'publish',
            'post_title' => $name
        ));
    }

    /**
     * Rename layout
     *
     * @param string $name New layout name
     */
    public function rename($name) {
        wp_update_post(array(
            'ID' => $this->layout_id,
            'post_title' => $name
        ));
    }

    /**
     * Delete layout from the database
     */
    public function delete() {
        wp_delete_post($this->layout_id);
    }

    /**
     * Get array representation of this layout
     *
     * @return array
     */
    public function to_array() {
        return array(
            'layout_id' => $this->layout_id,
            'name' => get_the_title($this->layout_id),
            'json' => $this->json,
            'time_created' => get_the_date('U', $this->layout_id)
        );
    }

}



