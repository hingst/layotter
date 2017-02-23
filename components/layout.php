<?php


class Layotter_Layout extends Layotter_Post {

    protected
        $layout_id = 0;

    const
        POST_TYPE_LAYOUTS = 'layotter_post_layout';

    public function __construct($id)
    {
        parent::__construct($id);
        $this->layout_id = $id;
    }

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

    public function update() {
        wp_update_post(array(
            'ID' => $this->layout_id,
            'post_type' => self::POST_TYPE_LAYOUTS,
            'meta_input' => array(
                Layotter::META_FIELD_JSON => $this->json
            ),
            'post_status' => 'publish'
        ));
    }

    public function rename($name) {
        wp_update_post(array(
            'ID' => $this->layout_id,
            'post_title' => $name
        ));
    }

    public function delete() {
        wp_delete_post($this->layout_id);
    }

    public function to_array() {
        return array(
            'layout_id' => $this->layout_id,
            'name' => get_the_title($this->layout_id),
            'json' => $this->json,
            'time_created' => get_the_date('U', $this->layout_id)
        );
    }

}



