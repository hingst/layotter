<?php

namespace Layotter\Components;

use Layotter\Acf\Adapter;
use Layotter\Core;
use Layotter\Errors;
use Layotter\Structures\FormMeta;

/**
 * Abstract class for editable components (options and elements)
 */
abstract class Editable {

    /**
     * @var int Editable ID (e.g. post ID)
     */
    protected $id = 0;

    /**
     * @var string Icon name from the Font Awesome set
     */
    protected $icon;

    /**
     * @var string Human readable title
     */
    protected $title;

    /**
     * @var string Editable type
     */
    protected $type;

    /**
     * Get ACF fields for this Editable
     *
     * @return array ACF fields
     */
    abstract public function get_fields();

    /**
     * Get values
     *
     * @return array Values
     */
    public function get_values() {
        $values = [];
        $fields = $this->get_fields();
        foreach ($fields as $field) {
            $values[ $field['name'] ] = Adapter::get_field_value($field['name'], $this->id);
        }
        return $values;
    }

    /**
     * Set element type
     *
     * @param string $type Type
     */
    public function set_type($type) {
        if (is_string($type)) {
            $this->type = $type;
        } else {
            Errors::invalid_argument_not_recoverable('type');
        }
    }

    /**
     * Get form data
     *
     * @return FormMeta Form meta data
     */
    public function get_form_meta() {
        return new FormMeta($this->title, $this->icon, wp_create_nonce(Adapter::get_nonce_name()), Adapter::get_form_html($this->get_fields(), $this->id), $this->id);
    }

    /**
     * Use wp_insert_post to trigger ACF hooks that read from $_POST and save custom fields
     */
    public function save_from_post_data() {
        $this->id = wp_insert_post([
            'post_type' => Core::POST_TYPE_EDITABLE,
            'meta_input' => [
                Core::META_FIELD_EDITABLE_TYPE => $this->type
            ],
            'post_status' => 'publish'
        ]);
    }

    /**
     * Use wp_update_post to trigger ACF hooks that read $_POST and save custom fields
     *
     * This is only used for templates.
     */
    public function update_from_post_data() {
        wp_update_post([
            'ID' => $this->id
        ]);
    }

    /**
     * ID getter
     *
     * @return int Editable ID (e.g. post ID)
     */
    public function get_id() {
        return $this->id;
    }

}
