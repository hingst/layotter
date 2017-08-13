<?php

namespace Layotter\Components;

use Layotter\Acf\Adapter;
use Layotter\Errors;

/**
 * Options for a post, row, columns or element
 */
class Options extends Editable {

    /**
     * @var string Post type context is required to determine which options fields should be visible
     */
    private $post_type_context = '';

    /**
     * Create Options instance
     *
     * @param int $id Options' post ID, 0 for new options
     */
    public function __construct($id = 0) {
        if (!is_int($id)) {
            Errors::invalid_argument_not_recoverable('id');
        }

        $this->id = intval($id);
        $this->icon = 'cog';

        if ($this->id !== 0) {
            $this->set_type(get_post_meta($this->id, self::META_FIELD_EDITABLE_TYPE, true));
        }
    }

    /**
     * Set options type (post, row, col, element)
     *
     * @param string $type Options type
     */
    public function set_type($type) {
        if (!self::is_valid_type($type)) {
            Errors::invalid_argument_not_recoverable('type');
        }

        $titles = [
            'post' => __('Post options', 'layotter'),
            'row' => __('Row options', 'layotter'),
            'col' => __('Column options', 'layotter'),
            'element' => __('Element options', 'layotter')
        ];
        $this->type = $type;
        $this->title = $titles[ $type ];
    }

    /**
     * Set post type context so Layotter can figure out if options are enabled for the current screen
     *
     * @param string $post_type Post type context, can be empty for no restrictions
     */
    public function set_post_type_context($post_type) {
        if (is_string($post_type) && post_type_exists($post_type)) {
            $this->post_type_context = strval($post_type);
        }
    }

    /**
     * Get ACF fields for these options
     *
     * @return array ACF fields
     */
    public function get_fields() {
        $field_groups = Adapter::get_filtered_field_groups([
            'post_type' => $this->post_type_context,
            'layotter' => $this->type . '_options'
        ]);

        $fields = [];
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

    /**
     * Check if a type name is a valid options type
     *
     * @param $type string Type name
     * @return bool
     */
    public static function is_valid_type($type) {
        return (is_string($type) && in_array($type, ['post', 'row', 'col', 'element']));
    }

}
