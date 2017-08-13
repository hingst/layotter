<?php

namespace Layotter\Components;

use Layotter\Acf\Adapter;
use Layotter\Structures\FormMeta;

/**
 * Abstract class for editable components (options and elements)
 */
abstract class Editable {

    const META_FIELD_EDITABLE_TYPE = 'layotter_editable_type';
    const POST_TYPE_EDITABLE = 'layotter_editable';

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
     * @return FormMeta Form meta data
     */
    public function get_form_meta() {
        return new FormMeta(
            $this->title,
            $this->icon,
            wp_create_nonce(Adapter::get_nonce_name()),
            Adapter::get_form_html($this->get_fields(), $this->id)
        );
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
     * ID getter
     *
     * @return int Editable ID (e.g. post ID)
     */
    public function get_id() {
        return $this->id;
    }

}
