<?php

namespace Layotter\Components;

use Layotter\Acf\Adapter;

/**
 * Abstract class for editable components (options and elements)
 */
abstract class Editable {

    const META_FIELD_EDITABLE_TYPE = 'layotter_editable_type';
    const POST_TYPE_EDITABLE = 'layotter_editable';

    protected $id = 0;
    protected $icon;
    protected $title;
    protected $type;

    abstract public function get_fields();

    /**
     * Get values
     *
     * @return array Values
     */
    public function get_values() {
        return get_fields($this->id);
    }

    /**
     * Set element type
     *
     * @param string $type Type
     */
    public function set_type($type) {
        $this->type = $type;
    }

    /**
     * Get form data
     *
     * @return array
     */
    public function get_form_data() {
        return array(
            'title' => $this->title,
            'icon' => $this->icon,
            'nonce' => wp_create_nonce('post'),
            'fields' => Adapter::get_form_html($this->get_fields(), $this->id)
        );
    }

    /**
     * Get form JSON
     *
     * @return string
     */
    public function get_form_json() {
        return json_encode($this->get_form_data());
    }

    /**
     * Use wp_insert_post to trigger ACF hooks that read from $_POST and save custom fields
     */
    public function save_from_post_data() {
        $this->id = wp_insert_post(array(
            'post_type' => self::POST_TYPE_EDITABLE,
            'meta_input' => array(
                self::META_FIELD_EDITABLE_TYPE => $this->type
            ),
            'post_status' => 'publish'
        ));
    }

    /**
     * Get Element ID
     *
     * @return int ID
     */
    public function get_id() {
        return $this->id;
    }

}
