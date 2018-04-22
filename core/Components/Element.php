<?php

namespace Layotter\Components;

use Layotter\Core;
use Layotter\Errors;
use Layotter\Settings;
use Layotter\Acf\Adapter;
use Layotter\Structures\ElementTypeMeta;

/**
 * All custom element types must extend this class
 */
abstract class Element extends Editable implements \JsonSerializable {

    /**
     * @var Options Element options
     */
    protected $options;

    /**
     * @var bool Whether the element instance is a template
     */
    protected $is_template = false;

    /**
     * @var string Human readable element type description
     */
    protected $description;

    /**
     * @var int|string ACF field group (ID or slug)
     */
    protected $field_group;

    /**
     * @var int Ordering number relative to other element types
     */
    protected $order = 0;

    /**
     * Must set $this->title, $this->description, $this->icon and $this->field_group
     * May set $this->order to override alphabetical ordering in the "Add Element" screen.
     */
    abstract protected function attributes();

    /**
     * Should output element's backend HTML
     *
     * @param array $fields Field values
     */
    abstract protected function backend_view($fields);

    /**
     * Should output element's frontend HTML
     *
     * @param array $fields Field values
     */
    abstract protected function frontend_view($fields);

    /**
     * backend_assets() can be used to enqueue scripts and styles for the backend
     */
    public static function backend_assets() {
    }

    /**
     * frontend_assets() can be used to enqueue scripts and styles for the frontend
     */
    public static function frontend_assets() {
    }

    /**
     * Create an element
     *
     * @param int $id Element's post ID, 0 for new elements
     */
    public function __construct($id = 0) {
        if (!is_int($id)) {
            Errors::invalid_argument_not_recoverable('id');
        }

        $this->attributes();
        $this->id = $id;

        if ($this->id !== 0) {
            $this->set_type(get_post_meta($id, Core::META_FIELD_EDITABLE_TYPE, true));
            if (get_post_meta($id, Core::META_FIELD_IS_TEMPLATE, true)) {
                $this->is_template = true;
            }
        }

        $this->options = Core::assemble_new_options('element');

        $this->register_frontend_hooks();
    }

    /**
     * Get ACF fields for this element
     *
     * @return array ACF fields
     * @throws \Exception If $this->field_group wasn't assigned correctly in $this->attributes()
     */
    public function get_fields() {
        // ACF field group can be provided as post id (int) or slug ('group_xyz')
        if (!is_int($this->field_group) && !is_string($this->field_group)) {
            throw new \Exception('$this->field_group must be assigned in attributes() (error in class ' . get_called_class() . ')');
        }

        if (is_int($this->field_group)) {
            $field_group = Adapter::get_field_group_by_id($this->field_group);
            $identifier = 'post_id';
        } else {
            $field_group = Adapter::get_field_group_by_key($this->field_group);
            $identifier = 'acf-field-group';
        }

        // check if the field group exists
        if (!$field_group) {
            throw new \Exception('No ACF field group found for ' . $identifier . '=' . $this->field_group . ' (error in class ' . get_called_class() . ')');
        }

        // return fields for the provided ACF field group
        return Adapter::get_fields($field_group);
    }

    /**
     * Register hooks for backend assets
     * Allows element types to enqueue scripts and styles in the backend.
     */
    public static function register_backend_hooks() {
        add_action('admin_footer', [get_called_class(), 'register_backend_hooks_helper']);
    }

    /**
     * Helper function for register_backend_hooks
     *
     * To make Layotter::is_enabled() work, the check is delayed until admin_footer.
     * Without the check, assets would be included on every page in the backend.
     */
    public static function register_backend_hooks_helper() {
        if (Core::is_enabled()) {
            call_user_func([get_called_class(), 'backend_assets']);
        }
    }

    /**
     * Register hooks for frontend assets
     * Allows element types to enqueue scripts and styles in the frontend.
     */
    private function register_frontend_hooks() {
        if (!is_admin()) {
            call_user_func([get_called_class(), 'frontend_assets']);
        }
    }

    /**
     * Check if this element type is enabled for a specific post
     *
     * @param int $post_id Post ID
     * @return bool
     */
    public function is_enabled_for($post_id) {
        if (!is_int($post_id)) {
            Errors::invalid_argument_not_recoverable('post_id');
        }

        $post_type = get_post_type($post_id);

        if (is_int($this->field_group)) {
            $field_group = Adapter::get_field_group_by_id($this->field_group);
        } else {
            $field_group = Adapter::get_field_group_by_key($this->field_group);
        }

        return Adapter::is_field_group_visible($field_group, [
            'post_id' => $post_id,
            'post_type' => $post_type,
            'layotter' => 'element'
        ]);
    }

    /**
     * Get element type meta data for display in the "Add Element" modal
     *
     * @return ElementTypeMeta Element type meta data
     */
    public function get_type_meta() {
        return new ElementTypeMeta($this->type, $this->title, $this->description, $this->icon, $this->order);
    }

    /**
     * Return array representation for use in json_encode()
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'options_id' => $this->options->get_id(),
            'view' => $this->get_backend_view(),
            'is_template' => $this->is_template
        ];
    }

    /**
     * Set or unset this element as a template
     *
     * @param bool $bool Set or unset
     */
    public function set_template($bool) {
        if (!is_bool($bool)) {
            Errors::invalid_argument_not_recoverable('bool');
        }

        $this->is_template = $bool;
        update_post_meta($this->id, Core::META_FIELD_IS_TEMPLATE, $bool);
    }

    /**
     * Check if this element is a template
     *
     * @return bool
     */
    public function is_template() {
        return $this->is_template;
    }

    /**
     * Get the backend view
     *
     * @return string Backend view HTML
     */
    public function get_backend_view() {
        ob_start();
        $this->backend_view($this->get_values());
        return ob_get_clean();
    }

    /**
     * Get the frontend view
     *
     * @param array $col_options Option values for the parent column
     * @param array $row_options Option values for the parent row
     * @param array $post_options Option values for the parent post
     * @param string $col_width Width of the parent column, e.g. '1/3'
     * @return string Frontend view HTML
     */
    public function get_frontend_view($col_options, $row_options, $post_options, $col_width) {
        ob_start();

        // provide more parameters than the function requires for backwards compatibility
        $this->frontend_view($this->get_values(), $col_width, $col_options, $row_options, $post_options);
        $element_html = ob_get_clean();

        if (has_filter('layotter/view/element')) {
            return apply_filters('layotter/view/element', $element_html, $this->options->get_values(), $col_options, $row_options, $post_options);
        } else {
            $html_wrapper = Settings::get_html_wrapper('elements');
            return $html_wrapper['before'] . $element_html . $html_wrapper['after'];
        }
    }

    /**
     * Get this element's options
     *
     * @return Options
     */
    public function get_options() {
        return $this->options;
    }

    /**
     * Set element options
     *
     * @param int $id Options ID
     */
    public function set_options($id) {
        if (!is_int($id)) {
            Errors::invalid_argument_not_recoverable('id');
        }

        $this->options = Core::assemble_options($id);
    }

}
