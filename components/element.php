<?php


/**
 * All custom element types must extend this class
 */
abstract class Layotter_Element extends Layotter_Editable {

    
    protected
        // internal use only
        $options,
        $is_template = false,
        // user-defined (mandatory)
        $description,
        $field_group,
        // user-defined (optional)
        $order = 0;

    const
        META_FIELD_IS_TEMPLATE = 'layotter_is_template';


    /**
     * Must assign $this->title, $this->description, $this->icon and $this->field_group
     *
     * May assign $this->order to override alphabetical ordering in the "Add Element" screen.
     */
    abstract protected function attributes();


    /**
     * Should output HTMl for the element's backend representation
     *
     * @param array $fields Field values
     */
    abstract protected function backend_view($fields);


    /**
     * Should output HTMl for the element's frontend representation
     *
     * @param array $fields Field values
     */
    abstract protected function frontend_view($fields);


    /**
     * backend_assets() is optional and should be used to enqueue scripts and styles for the backend
     */
    public static function backend_assets() {}


    /**
     * frontend_assets() is optional and should be used to enqueue scripts and styles for the backend
     */
    public static function frontend_assets() {}


    /**
     * Create a new element
     *
     * @param int $id Element's post ID, 0 for new elements.
     * @throws Exception If the ACF field group defined for this element doesn't exist
     */
    final public function __construct($id = 0) {
        $this->attributes();

        $this->id = intval($id);

        if ($this->id !== 0) {
            $this->set_type(get_post_meta($id, self::META_FIELD_EDITABLE_TYPE, true));
            if (get_post_meta($id, self::META_FIELD_IS_TEMPLATE, true)) {
                $this->is_template = true;
            }
        }

        $this->options = Layotter::assemble_new_options('element');

        $this->register_frontend_hooks();
    }

    /**
     * Get ACF fields for this element
     *
     * @return array ACF fields
     * @throws Exception If $this->field_group wasn't assigned correctly in $this->attributes()
     */
    final protected function get_fields() {
        // TODO: maybe clean fields to exclude stale fields?

        // ACF field group can be provided as post id (int) or slug ('group_xyz')
        if (!is_int($this->field_group) AND !is_string($this->field_group)) {
            throw new Exception('$this->field_group must be assigned in attributes() (error in class ' . get_called_class() . ')');
        }

        if (is_int($this->field_group)) {
            $field_group = Layotter_Acf_Abstraction::get_field_group_by_id($this->field_group);
            $identifier = 'post_id';
        } else {
            $field_group = Layotter_Acf_Abstraction::get_field_group_by_key($this->field_group);
            $identifier = 'acf-field-group';
        }

        // check if the field group exists
        if (!$field_group) {
            throw new Exception('No ACF field group found for ' . $identifier . '=' . $this->field_group . ' (error in class ' . get_called_class() . ')');
        }

        // return fields for the provided ACF field group
        return Layotter_Acf_Abstraction::get_fields($field_group);
    }


    /**
     * Register hooks for backend assets
     *
     * This allows element type developers to enqueue scripts and styles required to display this element
     * correctly in the backend.
     */
    final public static function register_backend_hooks() {
        add_action('admin_footer', array(get_called_class(), 'register_backend_hooks_helper'));
    }


    /**
     * Helper function for register_backend_hooks
     *
     * To make sure that Layotter::is_enabled() returns the correct value, the check is delayed until admin_footer.
     * Without the check, assets would be included on every single page in the backend.
     */
    final public static function register_backend_hooks_helper() {
        if (Layotter::is_enabled()) {
            call_user_func(array(get_called_class(), 'backend_assets'));
        }
    }


    /**
     * Register hooks for frontend assets
     *
     * This allows element type developers to enqueue scripts and styles required to display this element
     * correctly in the frontend.
     */
    final private function register_frontend_hooks() {
        if (!is_admin()) {
            call_user_func(array(get_called_class(), 'frontend_assets'));
        }
    }


    /**
     * Check if this element type is enabled for a specific post
     *
     * @param int $post_id Post ID
     * @return bool Whether this element type is enabled
     */
    final public function is_enabled_for($post_id) {
        $post_id = intval($post_id);
        $post_type = get_post_type($post_id);

        if (is_int($this->field_group)) {
            $field_group = Layotter_Acf_Abstraction::get_field_group_by_id($this->field_group);
        } else {
            $field_group = Layotter_Acf_Abstraction::get_field_group_by_key($this->field_group);
        }

        return Layotter_Acf_Abstraction::is_field_group_visible($field_group, array(
            'post_id' => $post_id,
            'post_type' => $post_type,
            'layotter' => 'element'
        ));
    }


    public function get_metadata() {
        return array(
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'order' => $this->order
        );
    }


    /**
     * Return array representation of this element for use in json_encode()
     *
     * PHP's JsonSerializable interface would be cleaner, but it's only available >= 5.4.0
     *
     * @return array Array representation of this element
     */
    public function to_array() {
        return array(
            'id' => $this->id,
            'options_id' => $this->options->get_id(),
            'view' => $this->get_backend_view(),
            'is_template' => $this->is_template
        );
    }


    public function set_template($bool) {
        $this->is_template = $bool;
        update_post_meta($this->id, self::META_FIELD_IS_TEMPLATE, $bool);
    }


    public function is_template() {
        return $this->is_template;
    }


    public function to_json() {
        return json_encode($this->to_array());
    }


    /**
     * Get the backend view
     *
     * @return string Backend view HTML
     */
    final public function get_backend_view() {
        ob_start();
        $this->backend_view($this->get_values());
        return ob_get_clean();
    }


    /**
     * Get the frontend view
     *
     * @param array $col_options Formatted options for the parent column
     * @param array $row_options Formatted options for the parent row
     * @param array $post_options Formatted options for the parent post
     * @param string $col_width Width of the parent column, e.g. '1/3'
     * @return string Frontend view HTML
     */
    final public function get_frontend_view($col_options, $row_options, $post_options, $col_width) {
        ob_start();
        $this->frontend_view($this->get_values(), $col_width, $col_options, $row_options, $post_options);
        $element_html = ob_get_clean();

        if (has_filter('layotter/view/element')) {
            return apply_filters('layotter/view/element', $element_html, $this->options->get_values(), $col_options, $row_options, $post_options);
        } else {
            $html_wrapper = Layotter_Settings::get_html_wrapper('elements');
            return $html_wrapper['before'] . $element_html . $html_wrapper['after'];
        }
    }


    public function get_options() {
        return $this->options;
    }


    public function set_options($id) {
        $this->options = Layotter::assemble_options($id);
        $this->options->set_post_type_context(get_post_type());
    }


    // for templates only
    public function update_from_post_data() {
        // wp_insert_post triggers ACF hooks that read from $_POST and save custom fields
        // it's ridiculous
        wp_update_post(array(
            'ID' => $this->id
        ));
    }



}
