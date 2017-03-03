<?php

namespace Layotter\Components;

use Layotter\Acf\Adapter;

/**
 * Options for a post, row, columns or element
 */
class Options extends Editable {

    private $post_type_context;

    /**
     * Create Options instance
     *
     * @param int $id Options' post ID, 0 for new options
     */
    public function __construct($id = 0) {
        $this->id = intval($id);
        $this->icon = 'cog';

        if ($this->id !== 0) {
            $this->set_type(get_post_meta($id, self::META_FIELD_EDITABLE_TYPE, true));
        }
    }

    /**
     * Set options type (post, row, col, element)
     *
     * @param string $type Options type
     */
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

    /**
     * Set post type context so Layotter can figure out if options are enabled for the current screen
     *
     * @param string $post_type Post type context
     * @throws \Exception If post tpe doesn't exist
     */
    public function set_post_type_context($post_type) {
        if (!post_type_exists($this->post_type_context)) {
            throw new \Exception('Unknown post type: ' . $this->post_type_context);
        }

        $this->post_type_context = strval($post_type);
    }

    /**
     * Get ACF fields for these options
     *
     * @return array ACF fields
     */
    protected function get_fields() {
        $field_groups = Adapter::get_filtered_field_groups(array(
            'post_type' => $this->post_type_context,
            'layotter' => $this->type . '_options'
        ));

        $fields = array();
        foreach ($field_groups as $field_group) {
            $fields = array_merge($fields, Adapter::get_fields($field_group));
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
