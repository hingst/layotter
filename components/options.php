<?php

/**
 * Options for a post, row, columns or element
 */
class Layotter_Options extends Layotter_Editable {

    private $post_type_context;

    final public function __construct($id = 0) {
        $this->id = intval($id);
        $this->icon = 'cog';

        if ($this->id !== 0) {
            $this->set_type(get_post_meta($id, self::META_FIELD_EDITABLE_TYPE, true));
        }
    }

    public function set_type($type) {
        $this->type = strval($type);
        $titles = array(
            'post' => __('Post options', 'layotter'),
            'row' => __('Row options', 'layotter'),
            'col' => __('Column options', 'layotter'),
            'element' => __('Element options', 'layotter')
        );
        $this->title = $titles[$this->type];
    }

    public function set_post_type_context($post_type) {
        $this->post_type_context = strval($post_type);
    }

    protected function get_fields() {
        if (!post_type_exists($this->post_type_context)) {
            throw new Exception('Unknown post type: ' . $this->post_type_context);
        }

        $field_groups = Layotter_Acf_Abstraction::get_filtered_field_groups(array(
            'post_type' => $this->post_type_context,
            'layotter' => $this->type . '_options'
        ));

        $fields = array();
        foreach ($field_groups as $field_group) {
            $fields = array_merge($fields, Layotter_Acf_Abstraction::get_fields($field_group));
        }

        return $fields;
    }

    /**
     * Check if this option type is enabled for the current post (i.e. an ACF field group exists)
     *
     * @return boolean Whether options are enabled
     */
    public function is_enabled() {
        return !empty($this->get_fields());
    }

}



